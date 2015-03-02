<?php /** MicroGridViewWidget */

namespace Micro\widgets;

use Micro\base\Type;
use Micro\db\Query;
use Micro\mvc\Widget;
use Micro\wrappers\Html;
use Micro\base\Exception;

/**
 * GridViewWidget class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
 * @subpackage widgets
 * @version 1.0
 * @since 1.0
 */
class GridViewWidget extends Widget
{
    /** @var int $page Current page on table */
    public $page = 0;
    /** @var int $limit Limit current rows */
    public $limit = 10;
    /** @var bool $filters Usage filters */
    public $filters = false;
    /** @var string $template Template render */
    public $template = '{counter}{table}{pager}';
    /** @var string $textCounter text for before counter */
    public $counterText = 'Sum: ';
    /** @var string $emptyText text to render if rows not found */
    public $emptyText = 'Elements not found';
    /** @var array $attributes attributes for table */
    public $attributes = [];
    /** @var array $attributesCounter attributes for counter */
    public $attributesCounter = [];
    /** @var array $attributesHeading attributes for heading */
    public $attributesHeading = [];
    /** @var array $tableConfig table configuration */
    public $tableConfig = [];
    /** @var array $paginationConfig parameters for PaginationWidget */
    public $paginationConfig = [];

    /** @var array $rows Rows from data */
    protected $rows;
    /** @var array $fields Fields of data */
    protected $fields = [];
    /** @var int $rowsCount Count rows */
    protected $rowsCount = 0;
    /** @var int $totalCount Total count data */
    protected $totalCount = 0;


    /**
     * Re-declare widget constructor
     *
     * @access public
     *
     * @param array $args arguments
     *
     * @result void
     * @throws Exception
     */
    public function __construct( array $args = [] )
    {
        parent::__construct( $args );

        if (empty($args['data'])) {
            throw new Exception('Argument "data" not initialized into GridViewWidget');
        }

        $this->limit = ($this->limit < 10) ? 10 : $this->limit;
        $this->page  = ($this->page < 0)   ? 0  : $this->page;

        if ($args['data'] instanceof Query) {
            $select               = $args['data']->select;

            $args['data']->select = 'COUNT(*)';
            $args['data']->single = true;
            $this->totalCount     = $args['data']->run()[0];

            $args['data']->select = $select;
            $args['data']->ofset  = $this->page*$this->limit;
            $args['data']->limit  = $this->limit;
            $args['data']->single = false;
            $args['data']         = $args['data']->run();
        } else {
            $this->totalCount = count($args['data']);
            $args['data'] = array_slice($args['data'], $this->page*$this->limit, $this->limit);
        }

        foreach ($args['data'] AS $model) {
            $this->rows[] = is_subclass_of($model, 'Micro\db\Model') ? $model : (object)$model;
        }

        $this->fields = (null !== $this->rows) ? array_keys(Type::getVars($this->rows[0])) : [];
    }

    /**
     * Initialize widget
     *
     * @access public
     *
     * @result void
     */
    public function init()
    {
        $this->rowsCount = count($this->rows);

        $this->paginationConfig['countRows']   = $this->totalCount;
        $this->paginationConfig['limit']       = $this->limit;
        $this->paginationConfig['currentPage'] = $this->page;

        $this->tableConfig = $this->tableConfig ?: $this->fields;
    }

    /**
     * Running widget
     *
     * @access public
     *
     * @return string
     */
    public function run()
    {
        if (!$this->rows) {
            return $this->emptyText;
        }

        return str_replace(
            ['{counter}', '{pager}', '{table}'],
            [ $this->getCounter(), $this->getPager(), $this->getTable() ],
            $this->template
        );
    }

    /**
     * Get counter
     *
     * @access protected
     *
     * @return string
     */
    protected function getCounter()
    {
        return Html::openTag('div', $this->attributesCounter) .
               $this->counterText . $this->rowsCount . Html::closeTag('div');
    }

    /**
     * Get pager
     *
     * @access protected
     *
     * @return string
     */
    protected function getPager()
    {
        if (!$this->rows) {
            return '';
        }

        ob_start();

        $pager = new PaginationWidget($this->paginationConfig);
        $pager->init();
        $pager->run();

        return ob_get_clean();
    }

    /**
     * Get table
     *
     * @access protected
     *
     * @return string
     */
    protected function getTable()
    {
        $table = Html::openTag('table', $this->attributes);
        $table .= $this->renderHeading();
        $table .= $this->renderFilters();
        $table .= $this->renderRows();
        $table .= Html::closeTag('table');
        return $table;
    }

    /**
     * Render heading
     *
     * @access protected
     *
     * @return string
     */
    protected function renderHeading()
    {
        if (!$this->tableConfig) {
            return '';
        }

        $result = Html::openTag('tr', $this->attributesHeading);
        foreach ($this->tableConfig AS $key=>$row) {
            $result .= Html::openTag('th');
            $result .= !empty($row['header']) ? $row['header'] : $key;
            $result .= Html::closeTag('th');
        }
        $result .= Html::closeTag('tr');

        return $result;
    }

    /**
     * Render filters
     *
     * @access protected
     *
     * @return null|string
     */
    protected function renderFilters()
    {
        if (!$this->filters) {
            return '';
        }
        $result = null;

        $result .= Html::beginForm(null, 'get');
        $result .= Html::openTag('tr');
        foreach ($this->tableConfig AS $key=>$row) {
            $result .= Html::openTag('td');
            $result .= !empty($row['filter']) ? $row['filter'] : null;
            $result .= Html::closeTag('td');
        }
        $result .= Html::closeTag('tr');
        $result .= Html::endForm();

        return $result;
    }

    /**
     * Render rows
     *
     * @access protected
     *
     * @return null|string
     */
    protected function renderRows()
    {
        $result = null;
        foreach ($this->rows AS $data) {
            $result .= Html::openTag('tr');
            foreach ($this->tableConfig AS $key => $row) {
                $result .= Html::openTag('td');
                if (!empty($row['class']) AND is_subclass_of($row['class'], 'Micro\widgets\GridColumn')) {
                    $primaryKey = $data->{ !empty($row['key']) ? $row['key'] : 'id' };
                    $result .= new $row['class'](
                        $row + ['str' => (null === $data) ?: $data, 'pKey' => $primaryKey]
                    );
                } elseif (!empty($row['value'])) {
                    $result .= eval('return ' . $row['value'] . ';');
                } else {
                    $result .= property_exists($data, $key) ? $data->$key : null;
                }
                $result .= Html::closeTag('td');
            }
            $result .= Html::closeTag('tr');
        }
        return $result;
    }
}