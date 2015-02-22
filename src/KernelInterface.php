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

/**
 * Kernel Interface
 *
 * @author Elliot Wright
 */
interface KernelInterface
{
    const MASTER_REQUEST = 1;
    const SUB_REQUEST = 2;

    /**
     * Boot the application kernel
     *
     * @return void
     */
    public function boot();

    /**
     * Get application environment
     *
     * @return string
     */
    public function getEnvironment();

    /**
     * Serve a request, with a response
     *
     * @return
     */
    public function serve(Request $request, $type = self::MASTER_REQUEST);
}
