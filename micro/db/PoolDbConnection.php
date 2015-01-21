<?php /** PoolDbConnectionMicro */

namespace Micro\db;

use Micro\base\Exception;

/**
 * PoolDbConnection class file.
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
    /** @var array $masters master servers */
    protected $masters = [];
    /** @var string $actual actual server */
    protected $actual;
    /** @var array $servers defined servers */
    protected $servers = [];
    /** @var string $curr current server */
    protected $curr;


    /**
     * Make pool of DbConnections
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

        if (!isset($params['masters'])) {
            $params['masters'] = $params['servers'][ $params['servers'][0] ];
        }

        $this->curr = isset($params['current']) ? $params['current'] : $params['servers'][0];
        $this->masters = isset($params['actual']) ? $params['actual'] : $params['masters'][0];

        foreach ($params['masters'] AS $key=>$master) {
            $this->masters[$key] = new DbConnection($master);
        }
        foreach ($params['servers'] AS $key=>$server) {
            $this->servers[$key] = new DbConnection($server);
        }
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
        $actual = $this->masters[$this->actual];
        $curr = $this->servers[$this->curr];

        switch ($name) {
            case 'insert':
            case 'update':
            case 'delete':
            case 'createTable':
            case 'clearTable': {
                $curr = $actual;
                break;
            }
        }

        if (!function_exists(array($curr, $name))) {
            throw new Exception('Method not existed into DB');
        }

        return call_user_func_array(array($curr, $name), $args);
    }
}