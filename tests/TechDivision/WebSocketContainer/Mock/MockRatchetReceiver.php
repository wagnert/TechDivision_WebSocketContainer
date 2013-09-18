<?php

/**
 * TechDivision\WebSocketContainer\Mock\MockRatchetReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\WebSocketContainer\Mock;

use TechDivision\WebSocketContainer\RatchetReceiver;

/**
 *
 * @package TechDivision\WebSocketContainer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockRatchetReceiver extends RatchetReceiver
{

    /**
     * (non-PHPdoc)
     * @see \TechDivision\WebSocketContainer\RatchetReceiver::getResourceClass()
     */
    public function getResourceClass()
    {
        return parent::getResourceClass();
    }
}