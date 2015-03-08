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

use Exception;
use ReflectionObject;
use Strident\Kernel\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kernel
 *
 * @author Elliot Wright
 */
abstract class AbstractKernel implements KernelInterface
{
    /**
     * @var bool
     */
    protected $booted;

    /**
     * @var mixed
     */
    protected $configuration;

    /**
     * @var object
     */
    protected $container;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var array
     */
    protected $modules;

    /**
     * @var object
     */
    protected $requestStack;

    /**
     * @var string
     */
    protected $rootDirectory;

    /**
     * @var bool
     */
    protected $safeMode;


    /**
     * Constructor
     *
     * @param string $environment
     * @param bool   $debug
     */
    public function __construct($environment, $debug = false)
    {
        $requestStackClass = $this->getRequestStackClass();

        $this->booted = false;
        $this->debug = $debug;
        $this->environment = $environment;
        $this->requestStack = new $requestStackClass();
        $this->safeMode = false;
    }

    /**
     * Serve a request, and provide a response.
     *
     * @param Request $request
     * @param int     $type
     *
     * @return Response
     */
    public function serve(Request $request, Response $response, $type = KernelInterface::MASTER_REQUEST)
    {
        try {
            $response = $this->processRequest($request, $response, $type);
        } catch (Exception $e) {
            $response = $this->processException($e, $request, $response, $type);
        }

        return $response;
    }

    /**
     * Attempt to process a request
     *
     * @param Request  $request
     * @param Response $response
     * @param int      $type
     *
     * @return Response
     */
    public function processRequest(Request $request, Response $response, $type)
    {
        $this->requestStack->push($request);

        if (!$this->isBooted()) {
            $this->boot();
        }

        return new Response("Hello world!", 200);
    }

    /**
     * Attempt to process an exception
     *
     * @param Exception $e
     * @param Request   $request
     * @param Response  $response
     * @param int       $type
     *
     * @return Response
     */
    public function processException(Exception $e, Request $request, Response $response, $type)
    {
        return new Response("Oh no!", 500);
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        if ($this->isBooted()) {
            return;
        }

        try {
            // @todo: Load compiled class cache here if there is one.

            $this->initialiseConfiguration();
            $this->initialiseContainer();
            $this->initialiseModules();
        } catch (Exception $e) {
            $this->setSafeMode(true);
            $this->boot();

            throw $e;
        }

        $this->booted = true;
    }

    /**
     * Get cache directory
     *
     * @return string
     */
    public function getCacheDirectory()
    {
        return $this->getRootDirectory() . "/cache/" . $this->getEnvironment();
    }

    /**
     * Get configuration directory
     *
     * @return string
     */
    public function getConfigurationDirectory()
    {
        return $this->getRootDirectory() . "/config/";
    }

    /**
     * Get container
     *
     * @return object
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get container class
     *
     * @return string
     */
    public function getContainerClass()
    {
        return "Strident\\Container\\Container";
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return ".php";
    }

    /**
     * Get log directory
     *
     * @return string
     */
    public function getLogDirectory()
    {
        return $this->getRootDirectory() . "/logs/";
    }

    /**
     * Get modules
     *
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Get request stack class
     *
     * @return string
     */
    public function getRequestStackClass()
    {
        return "Symfony\\Component\\HttpFoundation\\RequestStack";
    }

    /**
     * Get kernel root directory
     *
     * @return string
     */
    public function getRootDirectory()
    {
        if (null === $this->rootDirectory) {
            $reflection = new ReflectionObject($this);
            $this->rootDirectory = str_replace("\\", "/", dirname($reflection->getFileName()));
        }

        return $this->rootDirectory;
    }

    /**
     * Get safeMode
     *
     * @return bool
     */
    public function getSafeMode()
    {
        return $this->safeMode;
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
     * Initialise configuration
     *
     * @return void
     */
    protected function initialiseConfiguration()
    {
        $env = $this->getEnvironment();
        $ext = $this->getExtension();

        $file = require $this->getConfigurationDirectory() . "config_" . $env . $ext;

        $this->configuration = $file;
    }

    /**
     * Initialise the container
     *
     * @return void
     */
    protected function initialiseContainer()
    {
        $class = $this->getContainerClass();

        $this->container = new $class();
    }

    /**
     * Initialise all modules
     *
     * @return void
     */
    protected function initialiseModules()
    {
        if (!is_array($this->getModules()) || !count($this->getModules())) {
            return;
        }

        foreach ($this->getModules() as $module) {
            if ($module instanceof ModuleInterface) {
                $module->setContainer($this->getContainer());
                $module->boot();
            }
        }
    }

    /**
     * Register modules
     *
     * @param string $environment
     *
     * @return void
     */
    abstract public function registerModules($environment);

    /**
     * Set safeMode
     *
     * @param bool $safeMode
     *
     * @return $this
     */
    public function setSafeMode($safeMode)
    {
        $this->safeMode = $safeMode;

        return $this;
    }
}
