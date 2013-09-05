<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wickb
 * Date: 06.08.13
 * Time: 17:00
 * To change this template use File | Settings | File Templates.
 */

namespace TechDivision\WebSocketContainer;

use WebSocket\Socket;

class WebSocket extends Socket
{
    public function __construct($host = 'localhost', $port = 8000, $ssl = false)
    {
        parent::__construct($host, $port, $ssl);
    }

    public function readBuffer($resource)
    {
        return parent::readBuffer($resource);
    }

    public function getAllSockets()
    {
        return $this->allsockets;
    }
}