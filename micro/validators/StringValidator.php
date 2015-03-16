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
            $elementLength = strlen($model->$element);
            if (!empty($this->params['min'])) {
                $this->params['min'] = filter_var($this->params['min'], FILTER_VALIDATE_INT);
                if ($this->params['min'] > $elementLength) {
                    $this->errors[] = $element . ' error: minimal characters not valid.';
                    return false;
                }
            }
            if (!empty($this->params['max'])) {
                $this->params['max'] = filter_var($this->params['max'], FILTER_VALIDATE_INT);
                if ($this->params['max'] < $elementLength) {
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
        if (!empty($this->params['min'])) {
            $action .= ' if (this.value.length < ' . $this->params['min'] . ') { e.preventDefault(); this.focus();' .
                ' alert(\'Value lowest, minimum ' . $this->params['min'] . ' symbols\'); }';
        }
        if (!empty($this->params['max'])) {
            $action .= ' if (this.value.length > ' . $this->params['max'] . ') { e.preventDefault(); this.focus();' .
                ' alert(\'Value highest, maximum ' . $this->params['max'] . ' symbols\'); }';
        }
        return $action;
    }
}