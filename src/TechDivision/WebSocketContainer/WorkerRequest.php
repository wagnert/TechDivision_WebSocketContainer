<?php

/**
 * TechDivision\WebSocketContainer\WorkerRequest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\WebSocketContainer;

use TechDivision\ServletContainer\Http\HttpRequest;
use TechDivision\ServletContainer\Http\HttpResponse;
use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\Socket\Client;
use TechDivision\ServletContainer\Servlets\StaticResourceServlet;


/**
 * The stackable implementation that handles the request.
 *
 * @package     TechDivision\WebSocketContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class WorkerRequest extends \Stackable {

    /**
     * The client socket resource.
     * @var string
     */
    public $resource;

    /**
     * Initializes the request with the client socket.
     *
     * @param resource $resource The client socket instance
     * @return void
     */
    public function __construct($resource) {
        $this->resource = $resource;
    }

    /**
     * @see \Stackable::run()
     */
    public function run() {

        if ($this->worker) {
            // initialize a new client socket
            $client = new Client();

            // set the client socket resource
            $client->setResource($this->resource);

            // read a line from the client
            $line = $client->readLine();

            // unserialize the passed remote method
            $remoteMethod = unserialize($line);

            // check if a remote method has been passed
            if ($remoteMethod instanceof RemoteMethod) {

                try {

                    // load class name and session ID from remote method
                    $className = $remoteMethod->getClassName();
                    $sessionId = $remoteMethod->getSessionId();

                    // load the referenced application from the server
                    $application = $this->worker->findApplication($className);

                    // create initial context and lookup session bean
                    $instance = $application->lookup($className, $sessionId);

                    // prepare method name and parameters and invoke method
                    $methodName = $remoteMethod->getMethodName();
                    $parameters = $remoteMethod->getParameters();

                    // invoke the remote method call on the local instance
                    $response = call_user_func_array(array($instance, $methodName), $parameters);

                } catch (\Exception $e) {
                    $response = new \Exception($e);
                }

                try {

                    // send the data back to the client
                    $client->sendLine(serialize($response));

                    // close the socket immediately
                    $client->close();

                } catch (\Exception $e) {

                    // log the stack trace
                    error_log($e->__toString());

                    // close the socket immediately
                    $client->close();
                }

            } else {
                error_log('Invalid remote method call');
            }
        }
    }

    /**
     * Prepares the headers for the given response and returns them.
     *
     * @param Response $response The response to prepare the header for
     * @return string The headers
     * @todo This is a dummy implementation, headers has to be handled in request/response
     */
    public function prepareHeader(Response $response)
    {
        // prepare the content length
        $contentLength = strlen($response->getContent());

        // prepare the dynamic headers
        $response->addHeader("Content-Length", $contentLength);

        // return the headers
        return $response->getHeadersAsString();
    }
}