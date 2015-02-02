<?php /** MicroMultiDbConnection */

namespace Micro\db;

use Micro\base\Exception;


class MultiDbConnection {
    protected $servers;
    protected $curr;

    public function __construct( array $params = [] ) {
        if (!isset($params['servers'])) {
            throw new Exception('Servers not defined');
        }

        foreach ($params['servers'] AS $key=>$server) {
            $this->servers[$key] = new DbConnection($server, true);
        }

        $this->curr = key($this->servers);
    }

    public function __call($name, $args) {
        if (!method_exists($this->servers[$this->curr], $name)) {
            throw new Exception('Method not existed into DB');
        }

        return call_user_func_array(array($this->servers[$this->curr], $name), $args);
    }

    public function switchDB($name) {
        $this->curr = in_array($name, array_keys($this->servers)) ? $name : $this->servers[0];
    }
}