<?php

/**
 * This file is part of the Kernel package.
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 * @package Kernel
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strident\Kernel\Module;

/**
 * ModuleInterface
 *
 * @author Elliot Wright
 */
interface ModuleInterface
{
    /**
     * Boot this module
     *
     * @return void
     */
    public function boot();

    /**
     * setContainer
     *
     * @return $this
     */
    public function setContainer();
}
