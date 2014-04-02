<?php

/**
 * MicroQuery class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/antivir88/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license http://opensource.org/licenses/MIT
 * @package micro
 * @subpackage db
 * @version 1.0
 * @since 1.0
 */
class MQuery
{
	/** @var MicroConnection $_conn Current connect to DB */
	private $_conn;
	/** @var string $select selectable columns */
	public $select		= '*';
	/** @var boolean $distinct uniques rows */
	public $distinct	= false;
	/** @var string $where condition */
	public $where		= '';
	/** @var string $join joins tables */
	public $join		= '';
	/** @var string $order sorting result rows */
	public $order		= '';
	/** @var string $group grouping result rows */
	public $group		= '';
	/** @var string $having condition for result rows */
	public $having		= '';
	/** @var integer $limit count result rows */
	public $limit		= -1;
	/** @var integer $offset offset on strart result rows */
	public $ofset		= -1;
	/** @var array $params masks for where */
	public $params		= array();
	/** @var string $table table for query */
	public $table		= '';
	/** @var string $objectName class name form fetching */
	public $objectName	= '';
	/** @var boolean $single */
	public $single		= false;

	/**
	 * Construct class
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->getDbConnection();
	}
	/**
	 * Get connection to db
	 *
	 * @access public
	 * @global MRegistry
	 * @return void
	 */
	public function getDbConnection() {
		$this->_conn = MRegistry::get('db')->conn;
	}
	/**
	 * Add where
	 *
	 * @access public
	 * @param string $sql
	 * @param string $operand
	 * @return void
	 */
	public function addWhere($sql, $operand = 'AND') {
		$this->where .= ($this->where) ? ' '. $operand. ' (' . $sql . ')' : ' ' . $this->where. ' (' . $sql . ')' ;
	}
	/**
	 * Add search where
	 *
	 * @access public
	 * @param string $column
	 * @param string $keyword
	 * @param boolean $escaped
	 * @param string $operand
	 * @return void
	 */
	public function addSearch($column, $keyword, $escaped = false, $operand = 'AND') {
		$keyword = ($escaped) ? $keyword : '"%'.$keyword.'%"';
		$this->addWhere($column . ' LIKE ' . $keyword ,$operand);
	}
	/**
	 * Add not search where
	 *
	 * @access public
	 * @param string $column
	 * @param string $keyword
	 * @param boolean $escaped
	 * @param string $operand
	 * @return void
	 */
	public function addNotSearch($column, $keyword, $escaped, $operand = 'AND') {
		$keyword = ($escaped) ? $keyword : '"%'.$keyword.'%"';
		$this->addWhere($column . ' NOT LIKE ' . $keyword ,$operand);
	}
	/**
	 * Add in where
	 *
	 * @access public
	 * @param string $column
	 * @param array|string $params
	 * @param string $operand
	 * @return void
	 */
	public function addIn($column, $params, $operand = 'AND') {
		if (is_array($params)) {
			$params = "'" . implode("','", $params) . "'";
		}

		$this->addWhere($column . ' IN (' . $params . ')',$operand);
	}
	/**
	 * Add not in where
	 *
	 * @access public
	 * @param string $column
	 * @param array|string $params
	 * @param string $operand
	 * @return void
	 */
	public function addNotIn($column, $params, $operand = 'AND') {
		if (is_array($params)) {
			$params = "'" . implode("','", $params) . "'";
		}

		$this->addWhere($column . ' NOT IN (' . $params . ')',$operand);
	}
	/**
	 * Add between where
	 *
	 * @access public
	 * @param string $column
	 * @param mixed $strart
	 * @param mixed $stop
	 * @param string $operand
	 * @return void
	 */
	public function addBetween($column, $start, $stop, $operand = 'AND') {
		$this->addWhere($column . ' BETWEEN ' . $start . ' AND ' . $stop ,$operand);
	}
	/**
	 * Add not between where
	 *
	 * @access public
	 * @param string $column
	 * @param mixed $strart
	 * @param mixed $stop
	 * @param string $operand
	 * @return void
	 */
	public function addNotBetween($column, $start, $stop, $operand = 'AND') {
		$this->addWhere($column . ' BETWEEN ' . $start . ' AND ' . $stop ,$operand);
	}
	/**
	 * Add join
	 *
	 * @access public
	 * @param string $table
	 * @param string $condition
	 * @param string $type
	 * @return void
	 */
	public function addJoin($table, $cond, $type = 'LEFT') {
		$this->join .= ' ' . $type . ' JOIN ' . $table . ' ON ' . $cond;
	}
	/**
	 * Generate query string
	 *
	 * @access public
	 * @return string
	 */
	public function getQuery() {
		$query = 'SELECT ';
		$query .= ($this->distinct) ? 'DISTINCT ' : '';
		$query .= $this->select . ' FROM ' . $this->table;

		$query .= ($this->join) ? ' ' . $this->join : '';
		$query .= ($this->where) ? ' WHERE ' . $this->where : '';
		$query .= ($this->group) ? ' GROUP BY ' . $this->group : '';
		$query .= ($this->having) ? ' HAVING ' . $this->having : '';
		$query .= ($this->order) ? ' ORDER BY ' . $this->order : '';

		if ($this->limit >= 0) {
			$query .= ' LIMIT ';
			if ($this->ofset >= 0) {
				$query .= $this->ofset . ',';
			}
			$query .= $this->limit;
		}

		return $query;
	}
	/**
	 * Running this query
	 *
	 * @access public
	 * @param boolean $single
	 * @return mixed result's of query
	 */
	public function run($single = false) {
		$query = $this->_conn->prepare($this->getQuery().';');
		$query->setFetchMode(PDO::FETCH_CLASS, ucfirst($this->objectName), array('new'=>false));

		foreach ($this->params AS $name => $value) {
			$query->bindValue($name, $value);
		}

		if ($query->execute()) {
			if ($this->single) {
				return $query->fetch();
			} else {
				return $query->fetchAll();
			}
		}
		return false;
	}
}