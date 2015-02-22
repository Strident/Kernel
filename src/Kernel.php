<?php

/**
 * This file is part of the Kernel package.
 *
 * @package Kernel
 * @since   2015
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strident\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kernel
 *
 * @author Elliot Wright
 */
class Kernel implements KernelInterface
{
    /**
     * @var bool
     */
    protected $booted;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var string
     */
    protected $environment;


    /**
     * Constructor
     *
     * @param string $environment
     * @param bool   $debug
     */
    public function __construct($environment, $debug = false)
    {
        $this->booted = false;
        $this->debug = $debug;
        $this->environment = $environment;
    }

    /**
     * Serve a request, and provide a response.
     *
     * @param Request $request
     * @param int     $type
     *
     * @return Response
     */
    public function serve(Request $request, $type = KernelInterface::MASTER_REQUEST)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        // @todo: Initialise other components (container, etc)

        $this->booted = true;
    }

    /**
     * Is booted?
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * Is in debug mode?
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}
