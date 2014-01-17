<?php

/**
 * TechDivision\WebSocketContainer\Handlers\HandlerConfiguration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\WebSocketContainer\Handlers;

/**
 * Handler configuration.
 *
 * @package TechDivision\WebSocketContainer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class HandlerConfiguration implements HandlerConfig
{
    
    /**
     * The handler's name from the handler.xml configuration file.
     * 
     * @var string
     */
    protected handlerName;

    /**
     * The handler manager instance.
     * 
     * @var \TechDivision\WebSocketContainer\HandlerManager
     */
    protected $handlerManager;
    
    /**
     * Array with the servlet's init parameters found in the handler.xml configuration file.
     * 
     * @var array
     */
    protected $initParameter = array();

    /**
     * Initializes the handler configuration with the handler manager instance.
     *
     * @param \TechDivision\WebSocketContainer\HandlerManager $handlerManager
     *            The handler manager instance
     * @return void
     */
    public function __construct($handlerManager)
    {
        $this->handlerManager = $handlerManager;
    }

    /**
     * Returns the handler manager instance.
     *
     * @return \TechDivision\WebSocketContainer\HandlerManager The handler manager instance
     */
    public function getHandlerManager()
    {
        return $this->handlerManager;
    }

    /**
     * Returns the application instance.
     *
     * @return \TechDivision\WebSocketContainer\Application The application instance
     */
    public function getApplication()
    {
        return $this->getHandlerManager()->getApplication();
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
     * Returns the webapp base path.
     *
     * @return string The webapp base path
     */
    public function getWebappPath()
    {
        return $this->getApplication()->getWebappPath();
    }

    /**
     * Returns the path to the appserver webapp base directory.
     *
     * @return string The path to the appserver webapp base directory
     */
    public function getAppBase()
    {
        return $this->getApplication()->getAppBase();
    }
    
    /**
     * Set's the handler's Uname from the handler.xml configuration file.
     * 
     * @param string $handlerName The handler name
     * @return void
     */
    public function setHandlerName($handlerName)
    {
        $this->handlerName = $handlerName;
    }
    
    /**
     * Return's the handler's name from the handler.xml configuration file.
     * 
     * @return string The handler name
     */
    public function getHandlerName()
    {
        return $this->handlerName;
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