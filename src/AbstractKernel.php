<?php

/**
 * This file is part of the Kernel package.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strident\Component\Kernel;

use Strident\Component\Kernel\Module\ConsoleModuleInterface;
use Strident\Component\Kernel\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kernel
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 */
abstract class AbstractKernel implements KernelInterface
{
    /**
     * @var bool
     */
    private $booted;

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
     * @var string
     */
    protected $path;

    /**
     * @var object
     */
    private $requestStack;


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
    }

    /**
     * Serve a request, and provide a response.
     *
     * @param Request  $request
     * @param Response $response
     * @param int      $type
     *
     * @return Response
     */
    public function serve(Request $request, Response $response, $type = KernelInterface::MASTER_REQUEST)
    {
        try {
            $response = $this->processRequest($request, $response, $type);
        } catch (\Exception $e) {
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
     * @param \Exception $e
     * @param Request    $request
     * @param Response   $response
     * @param int        $type
     *
     * @return Response
     */
    public function processException(\Exception $e, Request $request, Response $response, $type)
    {
        return new Response("Oh no!", 500);
    }

    /**
     * {@inheritDoc}
     */
    public function boot($safe = false)
    {
        if ($this->isBooted()) {
            return;
        }

        try {
            // @todo: Load compiled class cache here if there is one.

            $this->initialiseConfiguration();
            $this->initialiseContainer();
            $this->initialiseModules();
        } catch (\Exception $e) {
            $this->boot(true);

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
        return $this->getPath() . "/cache/" . $this->getEnvironment();
    }

    /**
     * Get configuration directory
     *
     * @return string
     */
    public function getConfigurationDirectory()
    {
        return $this->getPath() . "/config/";
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
    final public function getContainerClass()
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
    final public function getExtension()
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
        return $this->getPath() . "/logs/";
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
     * Get kernel root directory
     *
     * @return string
     */
    public function getPath()
    {
        if (null === $this->path) {
            $reflection = new \ReflectionObject($this);
            $this->path = str_replace("\\", "/", dirname($reflection->getFileName()));
        }

        return $this->path;
    }

    /**
     * Get request stack class
     *
     * @return string
     */
    final public function getRequestStackClass()
    {
        return "Symfony\\Component\\HttpFoundation\\RequestStack";
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
    final public function isDebug()
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
                $module->build($this);
            }

            if ($module instanceof ConsoleModuleInterface) {
                $module->registerCommands($this);
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
}
