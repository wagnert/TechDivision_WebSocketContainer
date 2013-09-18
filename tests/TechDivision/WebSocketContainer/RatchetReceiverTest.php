<?php

/**
 * TechDivision\WebSocketContainer\RatchetReceiverTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\WebSocketContainer;

use TechDivision\ApplicationServer\AbstractTest;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Mock\MockContainer;
use TechDivision\ApplicationServer\Mock\MockReceiver;

/**
 *
 * @package TechDivision\WebSocketContainer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class RatchetReceiverTest extends AbstractTest
{

    /**
     * The receiver instance to test.
     * @var \TechDivision\ApplicationServer\MockReceiver
     */
    protected $receiver;

    /**
     * The initial context instance passed to the receiver.
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The container instance passed to the receiver.
     * @var \TechDivision\ApplicationServer\MockContainer
     */
    protected $container;

	/**
	 * Initializes the application instance to test.
	 *
	 * @return void
	 */
	public function setUp()
	{
	    $configuration = new Configuration();
	    $configuration->initFromFile('_files/appserver_initial_context.xml');
	    $this->initialContext = new InitialContext($configuration);
	    $this->container = new MockContainer($this->initialContext, $this->getContainerConfiguration(), $this->getMockApplications());
	    $this->receiver = new RatchetReceiver($this->initialContext, $this->container);
	}

    /**
     * Test receivers start method.
     *
     * @return void
     */
    public function testStart()
    {
        $this->receiver->start();
        $this->assertInstanceOf('TechDivision\WebSocketContainer\RatchetRequest', $this->receiver->app);
    }
}