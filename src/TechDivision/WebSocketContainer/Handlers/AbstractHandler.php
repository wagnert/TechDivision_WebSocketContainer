<?php

/**
 * TechDivision\WebSocketContainer\Handlers\AbstractHandler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\WebSocketContainer\Handlers;

/**
 * Abstract base class for all handlers.
 *
 * @package     TechDivision\WebSocketContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractHandler implements Handler
{
    
    /**
     * The handler configuration instance.
     * 
     * @var \TechDivision\WebSocketContainer\Handlers\HandlerConfig
     */
    protected $config;

    /**
     * (non-PHPdoc)
     * 
     * @see \TechDivision\WebSocketContainer\Handlers\Handler::init()
     */
    public function init(HandlerConfig $config)
    {
        $this->config = $config;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \TechDivision\WebSocketContainer\Handlers\Handler::getHandlerConfig()
     */
    public function getHandlerConfig()
    {
        return $this->config;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \TechDivision\WebSocketContainer\Handlers\Handler::getHandlerManager()
     */
    public function getHandlerManager()
    {
        return $this->getHandlerConfig()->getHandlerManager();
    }
    
    /**
     * (non-PHPdoc)
     * 
     * @see \TechDivision\WebSocketContainer\Handlers\Handler::getApplication()
     */
    public function getApplication()
    {
        return $this->getHandlerManager()->getApplication();
    }
}