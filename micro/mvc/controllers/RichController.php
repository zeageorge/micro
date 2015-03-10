<?php

namespace Micro\mvc;

use Micro\base\Exception;
use Micro\base\Registry;
use Micro\mvc\controllers\Controller;
use Micro\web\Response;

abstract class RichController extends Controller
{
    /** @var string $module Module of current request */
    public $module;
    /** @var integer $result Result status */
    public $status;
    /** @var string $format Format for response */
    public $format = 'json';


    /**
     * Define types for actions
     *
     * <code>
     *  // DELETE, GET, HEAD, OPTIONS, POST, PUT
     * public function actionsTypes() {
     *  return [
     *     'create' => 'POST',
     *     'read'   => 'GET',
     *     'update' => 'UPDATE'
     *     'delete' => 'DELETE'
     *  ];
     * }
     * </code>
     *
     * @return array
     */
    abstract function actionsTypes();

    public function __construct()
    {
        parent::__construct();

        $this->methodType = Registry::get('request')->getMethod() ?: 'GET';
    }

    /**
     * Run action
     *
     * @access public
     *
     * @param string $name action name
     *
     * @return void
     * @throws Exception
     */
    public function action($name = 'index')
    {


        $view = null;
        $actionClass = false;

        if (!method_exists($this, 'action' . ucfirst($name))) {
            $actionClass = $this->getActionClassByName($name);

            if (!$actionClass) {
                $this->status = 500;
                $this->response([ 'errorString'=>'Action "' . $name . '" not found into ' . get_class($this) ]);
            }
        }
        $filters = method_exists($this, 'filters') ? $this->filters() : [];

        // new logic - check headers
        $types = $this->actionsTypes();
        if (!empty($types[$name]) && $this->methodType !== $types[$name]) {
            $this->status = 500;
            $this->response([
              'errorString'=>'Action "'. $name .'" not run with method "'. $this->methodType .'" into '.get_class($this)
            ]);
        }

        // pre - operations
        $this->applyFilters($name, true, $filters, null);

        // running
        if ($actionClass) {
            $cl = new $actionClass;
            $view = $cl->run();
        } else {
            $view = $this->{'action' . ucfirst($name)}();
        }

        if (is_object($view)) {
            $view = (array) $view;
        }

        // new logic - check headers

        // post - operations
        echo $this->applyFilters($name, false, $filters, $view);
    }

    public function response( array $data = [] )
    {
        $headers = [];

        $response = new Response($data, $this->status, $headers);
    }
}