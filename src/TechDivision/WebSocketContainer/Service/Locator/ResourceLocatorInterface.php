<?php

/**
 * TechDivision\WebSocketContainer\Service\Locator\ResourceLocatorInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\WebSocketContainer\Service\Locator;

use Guzzle\Http\Message\RequestInterface;

/**
 * Interface for the resource locator instances.
 *
 * @package TechDivision\WebSocketContainer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
interface ResourceLocatorInterface
{

    /**
     * Tries to locate the handler that handles the request and returns the instance if 
     * one can be found.
     *
     * @param \Guzzle\Http\Message\RequestInterface $request
     *            The request instance
     * @return \TechDivision\WebSocketContainer\Handlers\Handler The handler that maps the request instance
     */
    public function locate(RequestInterface $request);
}