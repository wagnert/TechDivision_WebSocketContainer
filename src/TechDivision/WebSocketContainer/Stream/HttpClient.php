<?php

/**
 * TechDivision\WebSocketContainer\Stream\HttpClient
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\WebSocketContainer\Stream;

use TechDivision\ServletContainer\Interfaces\HttpClientInterface;
use TechDivision\ServletContainer\Http\HttpRequest;
use TechDivision\Stream\Client;

/**
 * The http client implementation that handles the request like a webserver
 *
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Johann Zelger <jz@techdivision.com>
 *              Philipp Dittert <p.dittert@techdivision.com>
 */
class HttpClient extends Client implements HttpClientInterface
{
    
    /**
     * The HttpRequest instance to use as factory.
     * @var \TechDivision\ServletContainer\Http\HttpRequest
     */
    protected $httpRequest;

    /**
     * The new line character.
     * @param $newLine
     */
    public function setNewLine($newLine) {
        $this->newLine = $newLine;
    }
    
    /**
     * Injects the HttpRequest instance to use as factory.
     * 
     * @param \TechDivision\ServletContainer\Interfaces\Request $request The request instance to use
     * @return void
     */
    public function injectHttpRequest($request) {
        $this->httpRequest = $request;
    }

    /**
     * Injects the Part instance.
     *
     * @param \TechDivision\ServletContainer\Interfaces\Part $part The part instance to use
     * @return void
     */
    public function injectHttpPart($part) {
        $this->httpPart = $part;
    }
    
    /**
     * @see \TechDivision\ServletContainer\Interfaces\HttpClientInterface::getHttpRequest()
     */
    public function getHttpRequest() {
        return $this->httpRequest;
    }

    /**
     * @see \TechDivision\ServletContainer\Interfaces\HttpClientInterface::getHttpPart()
     */
    public function getHttpPart() {
        return $this->httpPart;
    }

    /**
     * @see \TechDivision\ServletContainer\Interfaces\HttpClientInterface::receive()
     */
    public function receive()
    {
        
        // initialize the buffer
        $buffer = null;
        
        // read a chunk from the socket
        while ($buffer .= $this->read($this->getLineLength())) {
            // check if header finished
            if (false !== strpos($buffer, $this->getNewLine())) {
                break;
            }
        }
    }
}