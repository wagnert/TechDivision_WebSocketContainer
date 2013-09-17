<?php

/**
 * TechDivision\WebSocketContainer\HandlerManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\WebSocketContainer;

use Ratchet\MessageComponentInterface;
use TechDivision\WebSocketContainer\Exceptions\InvalidApplicationArchiveException;

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
     *
     * @var array
     */
    protected $handler = array();

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

        // deploy the web application and register the handlers
        $this->deployWebapps();
        $this->registerHandlers();

        // return the instance itself
        return $this;
    }

    /**
     *
     * @param
     *            $archive
     */
    protected function deployArchive($archive)
    {
        error_log(__METHOD__ . ' is not implemented!');
    }

    /**
     * Gathers all available archived webapps and deploys them for usage.
     *
     * @param
     *            void
     * @return void
     */
    protected function deployWebapps()
    {
        // gather all the available web application archives and deploy them
        foreach (new \RegexIterator(new \FilesystemIterator($this->getWebappPath()), '/^.*\.phar$/') as $archive) {
            $this->deployArchive($archive);
        }
    }

    /**
     * Finds all handlers which are provided by the webapps and initializes them.
     *
     * @return void
     */
    protected function registerHandlers()
    {

        // the phar files have been deployed into folders
        if (is_dir($folder = $this->getWebappPath())) {

            // it's no valid application without at least the handler.xml file
            if (! file_exists($web = $folder . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'handler.xml')) {
                return;
            }

            // load the application config
            $config = new \SimpleXMLElement(file_get_contents($web));

            /**
             *
             * @var $mapping \SimpleXMLElement
             */
            foreach ($config->xpath('/web-app/handler-mapping') as $mapping) {

                // try to resolve the mapped handler class
                $className = $config->xpath('/web-app/handler[handler-name="' . $mapping->{'handler-name'} . '"]/handler-class');

                if (! count($className)) {
                    throw new InvalidApplicationArchiveException(sprintf('No handler class defined for handler %s', $mapping->{'handler-name'}));
                }

                // get the string classname
                $className = (string) array_shift($className);

                // instantiate the handler
                $handler = $this->getApplication()->newInstance($className);

                // load the url pattern
                $urlPattern = (string) $mapping->{'url-pattern'};

                // make sure that the URL pattern always starts with a leading slash
                $urlPattern = ltrim($urlPattern, '/');

                // the handler is added to the dictionary using the complete request path as the key
                $this->addHandler('/' . $urlPattern, $handler);
            }
        }
    }

    /**
     *
     * @param array $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
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
     *            The handler to key to register with
     * @param \Ratchet\MessageComponentInterface $handler
     *            The handler to be registered
     */
    public function addHandler($key, MessageComponentInterface $handler)
    {
        $this->handler[$key] = $handler;
    }

    /**
     *
     * @return String
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
}