<?php

/**
 * TechDivision\WebSocketContainer\Handlers\HandlerConfig
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\WebSocketContainer\Handlers;

/**
 * Interface for the handler configuration.
 *
 * @package     TechDivision\WebSocketContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface HandlerConfig
{

    /**
     * Return's the handler's name from the handler.xml configuration file.
     *
     * @return string The handler name
     */
    public function getHandlerName();

    /**
     * Returns the handler manager instance.
     *
     * @return \TechDivision\WebSocketContainer\Handlers\HandlerManager The handler manager instance
     */
    public function getHandlerManager();
}