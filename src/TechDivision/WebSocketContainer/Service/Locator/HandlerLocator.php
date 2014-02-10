<?php

/**
 * TechDivision\WebSocketContainer\Service\Locator\HandlerLocator
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
namespace TechDivision\WebSocketContainer\Service\Locator;

use Guzzle\Http\Message\RequestInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * The handler resource locator implementation.
 *
 * @category  Appserver
 * @package   TechDivision\WebSocketContainer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class HandlerLocator implements ResourceLocatorInterface
{

    /**
     * The handler manager instance.
     *
     * @var \TechDivision\WebSocketContainer\HandlerManager
     */
    protected $handlerManager;

    /**
     * The collection with the initialized routes.
     *
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routes;

    /**
     * Initializes the locator with the actual handler manager instance.
     *
     * @param \TechDivision\WebSocketContainer\HandlerManager $handlerManager The handler manager instance
     * 
     * @return void
     */
    public function __construct($handlerManager)
    {
        $this->handlerManager = $handlerManager;
        $this->initRoutes();
    }

    /**
     * Returns the handler manager instance to use.
     *
     * @return \TechDivision\WebSocketContainer\HandlerManager The handler manager instance to use
     */
    public function getHandlerManager()
    {
        return $this->handlerManager;
    }

    /**
     * Returns the actual application instance.
     *
     * @return \TechDivision\WebSocketContainer\Application The application instance
     */
    public function getApplication()
    {
        return $this->getHandlerManager()->getApplication();
    }

    /**
     * Prepares a collection with routes generated from the available handlers
     * and their handler mappings.
     *
     * @return \Symfony\Component\Routing\RouteCollection The collection with the available routes
     */
    public function initRoutes()
    {
        
        // retrieve the registered handlers
        $handlerMappings = $this->getHandlerManager()->getHandlerMappings();
        $handlers = $this->getHandlerManager()->getHandlers();
        
        // prepare the collection with the available routes and initialize the route counter
        $this->routes = new RouteCollection();
        $counter = 0;
        
        // iterate over the available handlers and prepare the routes
        foreach ($handlerMappings as $urlPattern => $handlerName) {
            $handler = $handlers[$handlerName];
            $pattern = str_replace('/*', "/{placeholder_$counter}", $urlPattern);
            $route = new Route($pattern, array(
                $handler
            ), array(
                "{placeholder_$counter}" => '.*'
            ));
            $this->routes->add($counter ++, $route);
        }
    }

    /**
     * Returns the collection with the initialized routes.
     *
     * @return \Symfony\Component\Routing\RouteCollection The initialize routes
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Returns the handler identified by the request URI of
     * the passed request instance.
     *
     * @param \Guzzle\Http\Message\RequestInterface $request The request instance
     * 
     * @return void
     * @see \TechDivision\WebSocketContainer\Service\Locator\ResourceLocatorInterface::locate()
     */
    public function locate(RequestInterface $request)
    {

        // build the file-path of the request
        $path = $request->getPath();

        // check if the application is loaded by a VHost
        if ($this->getApplication()->isVhostOf($request->getHost()) === false) {
            $path = '/' . ltrim(str_replace("/{$this->getApplication()->getName()}", "/", $path), '/');
        }

        // load the route collection
        $routes = $this->getRoutes();

        // initialize the context for the routing
        $context = new RequestContext($path, $request->getMethod(), $request->getHost());

        // initialize the URL matcher
        $matcher = new UrlMatcher($routes, $context);

        // traverse the path to find matching handler
        do {

            try {
                $handler = $matcher->match($path);
                break;
            } catch (ResourceNotFoundException $rnfe) {
                $path = substr($path, 0, strrpos($path, '/'));
            }
            
        } while (strpos($path, '/') !== false);
        
        // check at least one handler has been found
        if (is_array($handler) === false || sizeof($handler) === 0) {
            throw new HandlerNotFoundException("Can't find handler for requested path $path");
        }
        
        // load and return the the handler instance from the matching result
        return current($handler);
    }
}
