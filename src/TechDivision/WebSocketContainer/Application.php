<?php

/**
 * TechDivision\WebSocketContainer\Application
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
    
namespace TechDivision\WebSocketContainer;


use TechDivision\ApplicationServer\Configuration;
use TYPO3\FLOW3\Annotations\Proxy;

/**
 * The application instance holds all information about the deployed application
 * and provides a reference to the servlet manager and the initial context.
 *
 * @package     TechDivision\WebSocketContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 */
class Application
{

    /**
     * Path to the container's host configuration.
     * @var string
     */
    const CONTAINER_HOST = '/container/host';

    /**
     * Holds the server for this application
     * @var \WebSocket\Server
     */
    protected $server;

    /**
     * The unique application name.
     * @var string
     */
    protected $name;

    /**
     * The host configuration.
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;

    /**
     * Passes the application name That has to be the class namespace.
     * 
     * @param type $name The application name
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     * 
     * @return \TechDivision\WebSocketContainer\Application The connected application
     */
    public function connect() {

       /* $this->server = new \WebSocket\Server('127.0.0.1', 8588, false); // host,port,ssl

// server settings:
        $this->server->setCheckOrigin(true);
        $this->server->setAllowedOrigin('foo.lh');
        $this->server->setMaxClients(100);
        $this->server->setMaxConnectionsPerIp(20);
        $this->server->setMaxRequestsPerMinute(1000);

        $this->server->registerApplication($this->name, self::getInstance());*/

        return $this;
    }
    
    /**
     * Returns the application name (that has to be the class namespace, 
     * e. g. TechDivision\Example).
     * 
     * @return string The application name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set's the host configuration.
     *
     * @param TechDivision\ApplicationServer\Configuration $configuration The host configuration
     * @return \TechDivision\WebSocketContainer\Application The application instance
     */
    public function setConfiguration($configuration) {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Returns the host configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The host configuration
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Returns the path to the appserver webapp base directory.
     *
     * @return string The path to the appserver webapp base directory
     */
    public function getAppBase() {
        return $this->getConfiguration()->getChild(self::CONTAINER_HOST)->getAppBase();
    }
    
    /**
     * Return's the path to the web application.
     * 
     * @return string The path to the web application
     */
    public function getWebappPath() {
        return $this->getAppBase() . DS . $this->getName();
    }

    /**
     * Return's the server software.
     *
     * @return string The server software
     */
    public function getServerSoftware() {
        return $this->getConfiguration()->getChild(self::CONTAINER_HOST)->getServerSoftware();
    }

    /**
     * Return's the path to the web application.
     *
     * @return string The path to the web application
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * Return's the server admin email.
     *
     * @return string The server admin email
     */
    public function getServerAdmin() {
        return $this->getConfiguration()->getChild(self::CONTAINER_HOST)->getServerAdmin();
    }
}