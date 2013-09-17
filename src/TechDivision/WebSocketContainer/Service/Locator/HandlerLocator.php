<?php

/**
 * TechDivision\WebSocketContainer\Service\Locator\HandlerLocator
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\WebSocketContainer\Service\Locator;

use TechDivision\WebSocketContainer\Service\Locator\ResourceLocatorInterface;
use Guzzle\Http\Message\RequestInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * The handler resource locator implementation.
 *
 * @package TechDivision\WebSocketContainer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
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
     * Initializes the locator with the actual handler manager instance.
     *
     * @param \TechDivision\WebSocketContainer\HandlerManager $handlerManager
     *            The handler manager instance
     * @return void
     */
    public function __construct($handlerManager)
    {
        $this->handlerManager = $handlerManager;
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
    public function getRouteCollection()
    {

        // retrieve the registered handlers
        $handlers = $this->handlerManager->getHandler();

        // prepare the collection with the available routes and initialize the route counter
        $routes = new RouteCollection();
        $counter = 0;

        // iterate over the available handlers and prepare the routes
        foreach ($handlers as $urlPattern => $handler) {
            $pattern = str_replace('/*', "/{placeholder_$counter}", $urlPattern);
            $route = new Route($pattern, array(
                $handler
            ), array(
                "{placeholder_$counter}" => '.*'
            ));
            $routes->add($counter ++, $route);
        }

        // return the collection with the routes
        return $routes;
    }

    /**
     * (non-PHPdoc)
     *
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
        $routes = $this->getRouteCollection();

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
        } while (strpos($path, '/') !== FALSE);

        // return the handler instance
        return current($handler);
    }
}
