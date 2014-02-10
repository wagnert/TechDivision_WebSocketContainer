<?php

/**
 * TechDivision\WebSocketContainer\Handlers\Handler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\WebSocketContainer\Handlers;

use TechDivision\WebSocketContainer\Handlers\HandlerConfig;
use Ratchet\MessageComponentInterface;

/**
 * Interface for all handlers.
 *
 * @package     TechDivision\WebSocketContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface Handler extends MessageComponentInterface {

    /**
     * Initializes the handler with the passed configuration.
     *
     * @param \TechDivision\WebSocketContainer\Handlers\HandlerConfig $config The configuration to initialize the handler with
     * @throws \TechDivision\WebSocketContainer\Handlers\HandlerException Is thrown if the configuration has errors
     * @return void
     */
    public function init(HandlerConfig $config);

    /**
     * Return's the servlet's configuration.
     *
     * @return \TechDivision\WebSocketContainer\Handlers\HandlerConfig The handler's configuration
     */
    public function getHandlerConfig();

    /**
     * Returns the servlet manager instance (context)
     *
     * @return \TechDivision\WebSocketContainer\Handlers\HandlerManager The handler manager instance
     */
    public function getHandlerManager();

    /**
     * Returns the application instance.
     *
     * @return \TechDivision\WebSocketContainer\Application The application instance
     */
    public function getApplication();
}
