<?php /** MicroDbRBAC */

namespace Micro\auth;

use Micro\db\Query;

/**
 * Database RBAC class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/antivir88/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
 * @subpackage auth
 * @version 1.0
 * @since 1.0
 */
class DbRbac extends Rbac
{
    /**
     * Constructor file RBAC
     *
     * @access public
     * @result void
     */
    public function __construct()
    {
        parent::__construct();

        if (!$this->conn->tableExists('`rbac_role`')) {
            $this->conn->createTable('`rbac_role`', [
                '`name` varchar(127) NOT NULL',
                '`type` int(11) NOT NULL DEFAULT \'0\'',
                '`based` varchar(127)',
                '`data` text',
                'UNIQUE KEY `name` (`name`)'
            ], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
        }
    }

    /**
     * Assign RBAC element into user
     *
     * @access public
     * @param integer $userId user ID
     * @param string $name assign element name
     * @return bool
     */
    public function assign($userId, $name)
    {
        if ($this->conn->exists('rbac_role', ['name'=>$name]) AND $this->conn->exists('user', ['id'=>$userId])) {
            return $this->conn->insert('rbac_user', ['role'=>$name, 'user'=>$userId]);
        }
        return false;
    }

    /**
     * Get raw roles
     *
     * @access public
     * @param int $pdo PHPDataObject fetch key
     * @return mixed
     */
    public function rawRoles($pdo = \PDO::FETCH_ASSOC)
    {
        $query = new Query;
        $query->table = 'rbac_role';
        $query->order = '`type` ASC';
        $query->single = false;
        return $query->run($pdo);
    }

    /**
     * Check privileges to operation
     *
     * @access public
     * @param integer $userId user id
     * @param string $action checked action
     * @param array $data action params
     * @return boolean
     */
    public function check($userId, $action, $data=[])
    {
        if (!$this->conn->exists('rbac_role', ['name'=>$action])) {
            return false;
        }

        return parent::check($userId, $action, $data);
    }

    /**
     * Add new element into RBAC rules
     *
     * @access public
     * @param string $name element name
     * @param int $type element type
     * @param string $based based element name
     * @param string $data element params
     * @return bool
     */
    public function create($name, $type = self::TYPE_ROLE, $based=null, $data=null)
    {
        if ($this->conn->exists('rbac_role', ['name'=>$name])) {
            return false;
        }

        if (!empty($based) AND !$this->conn->exists('rbac_role', ['name'=>$based])) {
            return false;
        }

        switch ($type) {
            case Rbac::TYPE_ROLE:
            case Rbac::TYPE_OPERATION:
            case Rbac::TYPE_PERMISSION:
                break;
            default:
                return false;
                break;
        }

        return $this->conn->insert('rbac_role', ['name'=>$name, 'type'=>$type, 'based'=>$based, 'data'=>$data]);
    }

    /**
     * Delete element from RBAC rules
     *
     * @access public
     * @param string $name element name
     * @result void
     */
    public function delete($name)
    {
        $tree = $this->searchRoleRecursive($this->tree($this->rawRoles()),$name);
        if ($tree) {
            $this->recursiveDelete($tree, $name);
        }
    }

    /**
     * Recursive delete roles from array
     *
     * @access public
     * @param array $tree elements tree
     * @return void
     */
    public function recursiveDelete(&$tree) {
        foreach ($tree AS $key=>$element) {
            $this->conn->delete('rbac_user', 'role=:name', [ 'name'=>$element['name'] ]);
            $this->conn->delete('rbac_role', 'name=:name', [ 'name'=>$element['name'] ]);

            if (isset($tree['childs'])) {
                $this->recursiveDelete($element['childs']);
            }
            unset($tree[$key]);
        }
    }
}