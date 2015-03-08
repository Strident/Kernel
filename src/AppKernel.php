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

namespace Strident\Kernel;

/**
 * AppKernel
 *
 * @author Elliot Wright
 */
class AppKernel extends AbstractKernel
{
    /**
     * {@inheritDoc}
     */
    public function registerModules($environment)
    {
        $modules = [];

        $this->modules = $modules;
    }
}
