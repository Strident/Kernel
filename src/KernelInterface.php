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

namespace Strident\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kernel Interface
 *
 * @author Elliot Wright <elliot@elliotwright.co>
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
     * @param Request  $request
     * @param Response $response
     * @param int      $type
     *
     * @return Response
     */
    public function serve(Request $request, Response $response, $type = self::MASTER_REQUEST);
}
