<?php /** MicroStringValidator */

namespace Micro\validators;

use Micro\base\Validator;
use Micro\db\Model;

/**
 * StringValidator class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
 * @subpackage validators
 * @version 1.0
 * @since 1.0
 */
class StringValidator extends Validator
{
    /**
     * Validate on server, make rule
     *
     * @access public
     *
     * @param Model $model checked model
     *
     * @return bool
     */
    public function validate($model)
    {
        foreach ($this->elements AS $element) {
            if (!property_exists($model, $element)) {
                $this->errors[] = 'Parameter ' . $element . ' not defined in class ' . get_class($model);
                return false;
            }
            $elementValue = $model->$element;

            if (array_key_exists('min', $this->params) AND $this->params['min']) {
                if ((integer)$this->params['min'] > strlen($elementValue)) {
                    $this->errors[] = $element . ' error: minimal characters not valid.';
                    return false;
                }
            }
            if (array_key_exists('max', $this->params) AND $this->params['max']) {
                if ((integer)$this->params['max'] < strlen($elementValue)) {
                    $this->errors[] = $element . ' error: maximal characters not valid.';
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Client-side validation, make js rule
     *
     * @access public
     *
     * @param Model $model checked model
     *
     * @return string
     */
    public function client($model)
    {
        $action = '';
        if (array_key_exists('min', $this->params)) {
            $action .= ' if (this.value.length < ' . $this->params['min'] . ') { e.preventDefault(); this.focus();' .
                ' alert(\'Value lowest, minimum ' . $this->params['min'] . ' symbols\'); }';
        }
        if (array_key_exists('max', $this->params)) {
            $action .= ' if (this.value.length > ' . $this->params['max'] . ') { e.preventDefault(); this.focus();' .
                ' alert(\'Value highest, maximum ' . $this->params['max'] . ' symbols\'); }';
        }
        return $action;
    }
}