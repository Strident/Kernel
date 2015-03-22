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

namespace Strident\Component\Kernel\Module;

use Strident\Component\Kernel\KernelInterface;

/**
 * AbstractModule
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 */
abstract class AbstractModule implements ConsoleModuleInterface, ModuleInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;


    /**
     * {@inheritDoc}
     */
    public function build(KernelInterface $kernel)
    {
    }

    /**
     * {@inheritDoc}
     */
    final public function getName()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $name = get_class($this);
        $pos = strrpos($name, "\\");

        return $this->name = (false === $pos ? $name : substr($name, $pos + 1));
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        if (null === $this->path) {
            $reflection = new \ReflectionObject($this);
            $this->path = str_replace("\\", "/", dirname($reflection->getFileName()));
        }

        return $this->path;
    }

    public function registerConfiguration()
    {
    }

    public function registerCommands()
    {
    }

    public function registerServices()
    {
    }
}
