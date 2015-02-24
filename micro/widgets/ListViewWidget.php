<?php /** MicroListViewWidget */

namespace Micro\widgets;

use Micro\base\Exception;
use Micro\db\Query;
use Micro\mvc\Widget;
use Micro\wrappers\Html;

/**
 * ListViewWidget class file.
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
class ListViewWidget extends Widget
{
    /** @var string $query query to database */
    public $query;
    /** @var int $elemsType elements of return query type */
    public $elemsType = \PDO::FETCH_ASSOC;
    /** @var string $view Name of view file */
    public $view;
    /** @var int $limit Limit current rows */
    public $limit = 10;
    /** @var int $page Current page on table */
    public $page = 0;
    /** @var array $paginationConfig parameters for PaginationWidget */
    public $paginationConfig = [];
    /** @var array $attributes attributes for dl */
    public $attributes = [];
    /** @var array $attributesElement attributes for dt */
    public $attributesElement = [];
    public $attributesCounter = [];
    public $counterText = '';
    public $template = '{counter}{elements}{pager}';

    /** @var int $rowCount summary lines */
    protected $rowCount = 0;
    /** @var array $rows Rows table */
    protected $rows = [];
    /** @var string $pathView Generate path to view file */
    protected $pathView = '';


    /**
     * Initialize widget
     *
     * @access public
     * @result void
     */
    public function init()
    {
        if (!$this->query instanceof Query) {
            throw new Exception('Query not defined or error type');
        }

        if (!$this->pathView OR !$this->view) {
            throw new Exception('Controller or view not defined');
        }

        if ($this->limit < 10) {
            $this->limit = 10;
        }

        $this->pathView .= '/' . $this->view . '.php';

        if (!file_exists($this->pathView)) {
            throw new Exception('View path not valid: ' . $this->pathView);
        }

        $this->rows = $this->query->run($this->elemsType);
        $this->rowCount = count($this->rows);

        $this->paginationConfig['countRows'] = $this->rowCount;
        $this->paginationConfig['limit'] = $this->limit;
        $this->paginationConfig['currentPage'] = $this->page;
    }

    /**
     * Running widget
     *
     * @access public
     * @return void
     */
    public function run()
    {
        $st = $i = $this->page * $this->limit;

        ob_start();

        echo Html::openTag('ul', $this->attributes);
        for (; $i < ($st + $this->limit); $i++) {
            if (!empty($this->rows[$i])) {
                echo Html::openTag('li', $this->attributesElement);

                $element = $this->rows[$i];
                include $this->pathView;

                echo Html::closeTag('li');
            }
        }
        echo Html::closeTag('ul');

        $elements = ob_get_clean();

        ob_start();
        $pager = new PaginationWidget($this->paginationConfig);
        $pager->init();
        $pager->run();
        $pagers = ob_get_clean();

        echo str_replace(
            array('{counter}', '{elements}', '{pager}'),
            array(
                Html::openTag('div',
                    $this->attributesCounter) . $this->counterText . $this->rowCount . Html::closeTag('div'),
                $elements,
                $pagers
            ),
            $this->template
        );
    }
}