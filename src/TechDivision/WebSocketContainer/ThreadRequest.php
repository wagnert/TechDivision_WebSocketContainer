<?php

/**
 * TechDivision\WebSocketContainer\ThreadRequest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\WebSocketContainer;

use TechDivision\ApplicationServer\AbstractThread;
use WebSocket;

/**
 * The thread implementation that handles the request.
 *
 * @package     TechDivision\WebSocketContainer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Johann Zelger <j.zelger@techdivision.com>
 */
class ThreadRequest extends AbstractThread {

    /**
     * Holds the container instance
     *
     * @var Container
     */
    public $container;

    /**
     * Holds the main socket resource
     *
     * @var resource
     */
    public $resource;

    /**
     * Holds access logger instance
     *
     * @var AccessLogger
     */
    protected $accessLogger;

    /**
     * Initializes the request with the client socket.
     *
     * @param Container $container The ServletContainer
     * @param resource $resource The client socket instance
     * @return void
     */
    public function __construct($container, $resource) {
        $this->container = $container;
        $this->resource = $resource;
    }

    /**
     * @see AbstractThread::main()
     */
    public function main() {

    }

    /**
     * Returns and inits an accesslogger
     *
     * @return AccessLogger
     */
    public function getAccessLogger()
    {
        if (!$this->accessLogger) {
            $this->accessLogger = new AccessLogger();
        }
        return $this->accessLogger;
    }

    /**
     * Returns the container instance.
     *
     * @return \TechDivision\WebSocketContainer\Container The container instance
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * Returns the array with the available applications.
     *
     * @return array The available applications
     */
    public function getApplications() {
        return $this->getContainer()->getApplications();
    }

    /**
     * @see \TechDivision\WebSocketContainer\Container::findApplication($servletRequest)
     */
    public function findApplication($servletRequest) {
        return $this->getContainer()->findApplication($servletRequest);
    }
}