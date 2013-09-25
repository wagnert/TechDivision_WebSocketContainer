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
     * Array with available VHost configurations.
     * @array
     */
    protected $vhosts = array();

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return \TechDivision\WebSocketContainer\Application The connected application
     */
    public function connect()
    {

        // initialize the class loader with the additional folders
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath());
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'classes');
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'lib');

        // prepare the VHost configurations
        foreach ($this->getConfiguration()->getChilds(Vhost::XPATH_CONTAINER_VHOSTS) as $vhost) {

            // check if vhost configuration belongs to application
            if ($this->getName() == ltrim($vhost->getAppBase(), '/')) {

                // prepare the aliases if available
                $aliases = array();
                foreach ($vhost->getChilds(Vhost::XPATH_CONTAINER_ALIAS) as $alias) {
                    $aliases[] = $alias->getValue();
                }

                // initialize VHost classname and parameters
                $vhostClassname = '\TechDivision\ApplicationServer\Vhost';
                $vhostParameter = array(
                    $vhost->getName(),
                    $vhost->getAppBase(),
                    $aliases
                );

                // register VHost in array with app base folder
                $this->vhosts[] = $this->newInstance($vhostClassname, $vhostParameter);
            }
        }

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
     * Return's the server software.
     *
     * @return string The server software
     */
    public function getServerSoftware()
    {
        return $this->getConfiguration()
            ->getChild(Vhost::XPATH_CONTAINER_HOST)
            ->getServerSoftware();
    }

    /**
     * Return's the server admin email.
     *
     * @return string The server admin email
     */
    public function getServerAdmin()
    {
        return $this->getConfiguration()
            ->getChild(Vhost::XPATH_CONTAINER_HOST)
            ->getServerAdmin();
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
     * Return's the applications available VHost configurations.
     *
     * @return array The available VHost configurations
     */
    public function getVhosts()
    {
        return $this->vhosts;
    }

    /**
     * Checks if the application is the VHost for the passed server name.
     *
     * @param string $serverName
     *            The server name to check the application being a VHost of
     * @return boolean TRUE if the application is the VHost, else FALSE
     */
    public function isVhostOf($serverName)
    {

        // check if the application is VHost for the passed server name
        foreach ($this->getVhosts() as $vhost) {

            // compare the VHost name itself
            if (strcmp($vhost->getName(), $serverName) === 0) {
                return true;
            }

            // then compare all aliases
            if (in_array($serverName, $vhost->getAliases())) {
                return true;
            }
        }
        return false;
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