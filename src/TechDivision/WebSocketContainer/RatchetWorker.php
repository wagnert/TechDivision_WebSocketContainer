<?php

/**
 * TechDivision\WebSocketContainer\RatchetWorker
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
 * @package   TechDivision\WebSocketContainer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
namespace TechDivision\WebSocketContainer;

use React\Socket\Server;
use React\EventLoop\Factory;
use Ratchet\Server\IoServer;
use Ratchet\MessageComponentInterface;

/**
 * Creates an open-ended socket to listen on a port for incoming connections.
 *
 * Events are delegated through this to attached applications.
 *
 * @category  Appserver
 * @package   TechDivision\WebSocketContainer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class RatchetWorker extends IoServer
{

    /**
     * Constructor to avoid using the factory method that leads to problems in PHPUnit tests.
     *
     * @param MessageComponentInterface $app     The request handler instance
     * @param integer                   $port    The port to listen to
     * @param string                    $address The IP address to listen to
     * 
     * @return void
     */
    public function __construct(MessageComponentInterface $app, $port = 80, $address = '0.0.0.0')
    {

        // initialize event loop and socket
        $this->loop   = Factory::create();
        $this->socket = new Server($this->loop);
        $this->socket->listen($port, $address);

        // set the request handler
        $this->app = $app;

        // enable garbage collection
        gc_enable();
        set_time_limit(0);
        ob_implicit_flush();

        // add event
        $this->socket->on('connection', array($this, 'handleConnect'));

        // initialize handlers
        $this->handlers = new \SplFixedArray(3);
        $this->handlers[0] = array($this, 'handleData');
        $this->handlers[1] = array($this, 'handleEnd');
        $this->handlers[2] = array($this, 'handleError');
    }
}
