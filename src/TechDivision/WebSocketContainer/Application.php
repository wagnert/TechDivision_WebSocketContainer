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

use Guzzle\Http\Message\RequestInterface;
use TechDivision\ApplicationServer\AbstractApplication;
use TechDivision\WebSocketContainer\HandlerManager;
use TechDivision\WebSocketContainer\Service\Locator\HandlerLocator;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\Vhost;

/**
 * The application instance holds all information about the deployed application
 * and provides a reference to the web socket manager and the initial context.
 *
 * @package TechDivision\WebSocketContainer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class Application extends AbstractApplication
{

    /**
     * The handler manager.
     *
     * @var \TechDivision\WebSocketContainer\HandlerManager
     */
    protected $handlerManager;

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return \TechDivision\WebSocketContainer\Application The connected application
     */
    public function connect()
    {

        // also initialize the vhost configuration
        parent::connect();

        // initialize the class loader with the additional folders
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath());
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'classes');
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'lib');

        // initialize the handler manager instance
        $handlerManager = $this->newInstance('TechDivision\WebSocketContainer\HandlerManager', array(
            $this
        ));
        $handlerManager->initialize();

        // set the handler manager
        $this->setHandlerManager($handlerManager);

        // return the instance itself
        return $this;
    }

    /**
     * Sets the applications handler manager instance.
     *
     * @param \TechDivision\WebSocketContainer\HandlerManager $handlerManager
     *            The handler manager instance
     * @return \TechDivision\WebSocketContainer\Application The application instance
     */
    public function setHandlerManager(HandlerManager $handlerManager)
    {
        $this->handlerManager = $handlerManager;
        return $this;
    }

    /**
     * Return the handler manager instance.
     *
     * @return \TechDivision\WebSocketContainer\HandlerManager The handler manager instance
     */
    public function getHandlerManager()
    {
        return $this->handlerManager;
    }

    /**
     * Locates the handler for the passed request.
     *
     * @param \Guzzle\Http\Message\RequestInterface $request
     * @return \Ratchet\MessageComponentInterface
     */
    public function locate(RequestInterface $request)
    {
        $className = 'TechDivision\WebSocketContainer\Service\Locator\HandlerLocator';
        $handlerLocator = $this->newInstance($className, array(
            $this->getHandlerManager()
        ));
        return $handlerLocator->locate($request);
    }
}