<?php

/**
 * TechDivision\WebSocketContainer\RatchetReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_WebSocketContainer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
namespace TechDivision\WebSocketContainer;

use TechDivision\ApplicationServer\AbstractReceiver;
use Ratchet\Server\IoServer;

/**
 * This class implements a web socket receiver based on Ratchet, a WebSocket
 * server implementation for PHP.
 * 
 * @category  Appserver
 * @package   TechDivision_WebSocketContainer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 * @link      http://socketo.me
 */
class RatchetReceiver extends AbstractReceiver
{

    /**
     * Returns the resource class used to create a new socket.
     *
     * @return string The resource class name
     */
    protected function getResourceClass()
    {
        return 'TechDivision\Socket\Server';
    }

    /**
     * Starts the receiver and the Ratchet server.
     * 
     * @return void
     * @see \TechDivision\ApplicationServer\AbstractReceiver::start()
     */
    public function start()
    {

        // create a custom ratchet request instance
        $requestInstance = $this->newInstance($this->getThreadType(), array(
            $this->getContainer()
                ->getApplications()
        ));

        // initialize and start the ratchet worker instance
        $workerInstance = $this->newInstance($this->getWorkerType(), array(
            $requestInstance,
            $this->getPort(),
            $this->getAddress()
        ));

        // log a message that the container has been started successfully
        $this->getInitialContext()->getSystemLogger()->info(
            sprintf(
                'Successfully started receiver for container %s, listening on IP: %s Port: %s Number of workers started: %s, Workertype: %s',
                $this->getContainer()->getContainerNode()->getName(),
                $this->getAddress(),
                $this->getPort(),
                $this->getWorkerNumber(),
                $this->getWorkerType()
            )
        );
        
        // start the web socket server
        $workerInstance->run();
    }
}
