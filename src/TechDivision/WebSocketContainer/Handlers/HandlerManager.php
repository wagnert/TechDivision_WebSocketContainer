<?php

/**
 * TechDivision\WebSocketContainer\Handlers\HandlerManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\WebSocketContainer\Handlers;

/**
 * The handler manager handles the handlers registered for the application.
 *
 * @package TechDivision\WebSocketContainer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class HandlerManager
{

    /**
     * The application instance.
     *
     * @var \TechDivision\WebSocketContainer\Application
     */
    protected $application;

    /**
     * The array with the handlers.
     * 
     * @var array
     */
    protected $handler = array();
    
    /**
     * Array that contains the handler mappings
     * 
     * @var array
     */
    protected $handlerMappings = array();
    
    /**
     * Array with the handler's init parameters found in the handler.xml configuration file.
     * 
     * @var array
     */
    protected $initParameter = array();

    /**
     * Set's the application instance.
     *
     * @param \TechDivision\WebSocketContainer\Application $application
     *            The application instance
     * @return void
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return \TechDivision\WebSocketContainer\Application The connected application
     */
    public function initialize()
    {
        $this->registerHandlers();
        return $this;
    }
    
    /**
     * Finds all handlers which are provided by the webapps and initializes them.
     *
     * @return void
     * @throws \TechDivision\WebSocketContainer\InvalidHandlerClassException 
     *     Is thrown if a no handler class has been defined in handler configuration
     * @throws \TechDivision\WebSocketContainer\InvalidHandlerMappingException
     *     Is thrown if a no handler mapping relates to a invalid handler class
     */
    protected function registerHandlers()
    {
    
        // the phar files have been deployed into folders
        if (is_dir($folder = $this->getWebappPath())) {
    
            // it's no valid application without at least the web.xml file
            if (! file_exists($web = $folder . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'handler.xml')) {
                return;
            }
    
            // load the application config
            $config = new \SimpleXMLElement(file_get_contents($web));
    
            // initialize the context by parsing the context-param nodes
            foreach ($config->xpath('/web-app/context-param') as $contextParam) {
                $this->addInitParameter((string) $contextParam->{'param-name'}, (string) $contextParam->{'param-value'});
            }
    
            // initialize the handlers by parsing the handler-mapping nodes
            foreach ($config->xpath('/web-app/handler') as $handler) {
    
                // load the handler name and check if it already has been initialized
                $handlerName = (string) $handler->{'handler-name'};
                if (array_key_exists($handlerName, $this->handler)) {
                    continue;
                }
    
                // try to resolve the mapped handler class
                $className = (string) $handler->{'handler-class'};
                if (! count($className)) {
                    throw new InvalidHandlerClassException(sprintf('No handler class defined for handler %s', $handler->{'handler-class'}));
                }
    
                // instantiate the handler
                $instance = $this->getApplication()->newInstance($className);
    
                //  initialize the handler configuration
                $handlerConfig = $this->getApplication()->newInstance('TechDivision\WebSocketContainer\Handlers\HandlerConfiguration', array(
                    $this
                ));
    
                // set the unique handler name
                $handlerConfig->setHandlerName($handlerName);
    
                // append the init params to the handler configuration
                foreach ($handler->{'init-param'} as $initParam) {
                    $handlerConfig->addInitParameter((string) $initParam->{'param-name'}, (string) $initParam->{'param-value'});
                }
    
                // initialize the handler
                $instance->init($handlerConfig);
    
                // the handler is added to the dictionary using the complete request path as the key
                $this->addHandler($handlerName, $instance);
            }
    
            // initialize the handlers by parsing the handler-mapping nodes
            foreach ($config->xpath('/web-app/handler-mapping') as $mapping) {
    
                // load the url pattern and the handler name
                $urlPattern = (string) $mapping->{'url-pattern'};
                $handlerName = (string) $mapping->{'handler-name'};
    
                // make sure that the URL pattern always starts with a leading slash
                $urlPattern = ltrim($urlPattern, '/');
    
                // the handler is added to the dictionary using the complete request path as the key
                if (!array_key_exists($handlerName, $this->handlers)) {
                    throw new InvalidHandlerMappingException(sprintf("Can't find handler %s for url-pattern %s", $handlerName, $urlPattern));
                }
    
                // append the url-pattern - handler mapping to the array
                $this->handlerMappings['/' . $urlPattern] = (string) $mapping->{'handler-name'};
    
                $this->application->getInitialContext()->getSystemLogger()->debug(
                        sprintf('Successfully initialized handler %s for url-pattern %s in application %s',
                                $handlerName, $urlPattern, $this->application->getName()));
            }
        }
    }

    /**
     * Set's the array with all registered handlers.
     * 
     * @param array $handler An array with the web socket handlers to be registered
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * Return's the array with all registered handlers.
     * 
     * @return array An array with the initialized web socket handlers
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Registers a handler under the passed key.
     *
     * @param string $key
     *            The key to register with the handler with
     * @param \TechDivision\WebSocketContainer\Handlers\Handler $handler
     *            The handler to be registered
     */
    public function addHandler($key, Handler $handler)
    {
        $this->handler[$key] = $handler;
    }
    
    /**
     * Returns the handler mappings found in the
     * configuration file.
     * 
     * @return array The handler mappings
     */
    public function getHandlerMappings()
    {
        return $this->handlerMappings;
    }
    
    /**
     * Returns the handler for the passed URL mapping.
     * 
     * @param string $urlMapping The URL mapping to return the handler for
     * @return \TechDivision\WebSocktContainer\Handlers\Handler The handler instance
     */
    public function getHandlerByMapping($urlMapping)
    {
        if (array_key_exists($urlMapping, $this->handlerMappings)) {
            return $this->getHandler($this->handlerMappings[$urlMapping]);
        }
    }

    /**
     * Returns the webapp path for the application.
     * 
     * @return string The application's webapp path
     */
    public function getWebappPath()
    {
        return $this->getApplication()->getWebappPath();
    }

    /**
     * Returns the application instance.
     *
     * @return \TechDivision\WebSocketContainer\Application The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the host configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The host configuration
     */
    public function getConfiguration()
    {
        return $this->getApplication()->getConfiguration();
    }
    
    /**
     * Register's the init parameter under the passed name.
     * 
     * @param string $name Name to register the init parameter with
     * @param string $value The value of the init parameter
     */
    public function addInitParameter($name, $value)
    {
        $this->initParameter[$name] = $value;
    }
    
    /**
     * Return's the init parameter with the passed name.
     * 
     * @param string $name Name of the init parameter to return
     */
    public function getInitParameter($name)
    {
        if (array_key_exists($name, $this->initParameter)) {
            return $this->initParameter[$name];
        }
    }
}