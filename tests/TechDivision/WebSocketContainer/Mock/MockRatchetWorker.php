<?php

/**
 * TechDivision\WebSocketContainer\Mock\MockRatchetWorker
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\WebSocketContainer\Mock;

use TechDivision\WebSocketContainer\RatchetWorker;
use Ratchet\MessageComponentInterface;

/**
 *
 * @package TechDivision\WebSocketContainer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockRatchetWorker extends RatchetWorker
{

    /**
     * Overwrites the default constructor to avoid opening the socket.
     *
     * @param MessageComponentInterface $app The request instance
     * @param integer $port The port passed
     * @param string $address The IP address passed
     */
    public function __construct(MessageComponentInterface $app, $port = 80, $address = '0.0.0.0')
    {
        $this->app = $app;
    }

    /**
     * (non-PHPdoc)
     * @see \Ratchet\Server\IoServer::run()
     */
    public function run()
    {}
}