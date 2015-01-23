<?php /** PoolDbConnectionMicro */

namespace Micro\db;

use Micro\base\Exception;

/**
 * PoolDbConnection class file.
 *
 * For master-slave's configuration
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
 * @subpackage db
 * @version 1.0
 * @since 1.0
 */
class PoolDbConnection
{
    /** @var DbConnection $master master server */
    protected $master = null;
    /** @var array $servers defined slaves servers */
    protected $servers = [];
    /** @var string $curr current slave server */
    protected $curr;


    /**
     * Make pool of DbConnections
     *
     * If master configuration not defined using first slave server
     *
     * @access public
     *
     * @param array $params params to make
     *
     * @throws \Micro\base\Exception
     */
    public function __construct( array $params = [] ) {
        if (!isset($params['servers'])) {
            throw new Exception('Servers not defined');
        }

        if (!isset($params['master'])) {
            $params['master'] = $params['servers'][ $params['servers'][0] ];
        }

        $this->master = new DbConnection($params['master']);

        foreach ($params['servers'] AS $key=>$server) {
            $this->servers[$key] = new DbConnection($server);
        }

        $this->curr = isset($params['current']) ? $params['current'] : $params['servers'][0];
    }

    /**
     * Proxy to good server
     *
     * @access public
     *
     * @param string $name called function
     * @param mixed $args arguments of function
     *
     * @return mixed
     * @throws \Micro\base\Exception
     */
    public function __call($name, $args) {
        $curr = $this->servers[$this->curr];

        switch ($name) {
            case 'insert':
            case 'update':
            case 'delete':
            case 'createTable':
            case 'clearTable': {
                $curr = $this->master;
                break;
            }
        }

        if (!function_exists(array($curr, $name))) {
            throw new Exception('Method not existed into DB');
        }

        return call_user_func_array(array($curr, $name), $args);
    }
}