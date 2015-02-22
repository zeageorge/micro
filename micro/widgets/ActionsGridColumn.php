<?php /** MicroActionsGridColumn */

namespace Micro\widgets;

use Micro\wrappers\Html;

/**
 * Actions grid column class file.
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
class ActionsGridColumn extends GridColumn
{
    /**
     * Convert object to string
     *
     * @access public
     * @return mixed
     */
    public function __toString()
    {
        if (!array_key_exists('link', $this->params) OR empty($this->params['link'])) {
            return 'Link for actions column not defined!';
        }
        if (!array_key_exists('template', $this->params) OR empty($this->params['template'])) {
            $this->params['template'] = '{view} {edit} {delete}';
        }

        $viewLink = (array_key_exists('viewLink', $this->params) ? $this->params['viewLink'] : $this->params['link'] . '/');
        $r = Html::href(
            array_key_exists('viewText', $this->params) ? $this->params['viewText'] : 'view',
            $viewLink . $this->params['pKey']
        );

        $editLink = (array_key_exists('editLink', $this->params) ? $this->params['editLink'] : $this->params['link'] . '/edit/');
        $w = Html::href(
            array_key_exists('editText', $this->params) ? $this->params['editText'] : 'edit',
            $editLink . $this->params['pKey']
        );

        $deleteLink = (array_key_exists('deleteLink', $this->params) ? $this->params['deleteLink'] : $this->params['link'] . '/del/');
        $d = Html::href(
            array_key_exists('deleteText', $this->params) ? $this->params['deleteText'] : 'delete',
            $deleteLink . $this->params['pKey'],
            ['onclick' => 'return confirm(\'Are you sure?\')']
        );

        return str_replace('{view}', $r, str_replace('{edit}', $w,
            str_replace('{delete}', $d, $this->params['template'])
        ));
    }
}