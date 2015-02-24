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
        if (empty($this->params['link'])) {
            return 'Link for actions column not defined!';
        }
        if (empty($this->params['template'])) {
            $this->params['template'] = '{view} {edit} {delete}';
        }

        $viewLink = (!empty($this->params['viewLink']) ? $this->params['viewLink'] : $this->params['link'] . '/');
        $r = Html::href(
            !empty($this->params['viewText']) ? $this->params['viewText'] : 'view',
            $viewLink . $this->params['pKey']
        );

        $editLink = (!empty($this->params['editLink']) ? $this->params['editLink'] : $this->params['link'] . '/edit/');
        $w = Html::href(
            !empty($this->params['editText']) ? $this->params['editText'] : 'edit',
            $editLink . $this->params['pKey']
        );

        $deleteLink = (!empty($this->params['deleteLink']) ? $this->params['deleteLink'] : $this->params['link'] . '/del/');
        $d = Html::href(
            !empty($this->params['deleteText']) ? $this->params['deleteText'] : 'delete',
            $deleteLink . $this->params['pKey'],
            ['onclick' => 'return confirm(\'Are you sure?\')']
        );

        return str_replace('{view}', $r, str_replace('{edit}', $w,
            str_replace('{delete}', $d, $this->params['template'])
        ));
    }
}