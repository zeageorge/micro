<?php /** MicroFileValidator */

namespace Micro\validators;

use Micro\base\Validator;
use Micro\db\Model;
use Micro\web\Uploader;

/**
 * EmailValidator class file.
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
class FileValidator extends Validator
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
            $files = new Uploader;
            if (array_key_exists('maxFiles', $this->params) AND (count($files->files) > $this->params['maxFiles'])) {
                $this->errors[] = 'Too many files in parameter ' . $element;
                return false;
            }
            foreach ($files->files AS $fContext) {
                if (array_key_exists('types', $this->params) AND (strpos($this->params['types'],
                            $fContext['type']) === false)
                ) {
                    $this->errors[] = 'File ' . $fContext['name'] . ' not allowed type';
                    return false;
                }
                if (array_key_exists('minSize', $this->params) AND ($fContext['size'] < $this->params['minSize'])) {
                    $this->errors[] = 'File ' . $fContext['name'] . ' too small size';
                    return false;
                }
                if (array_key_exists('maxSize', $this->params) AND ($fContext['type'] > $this->params['maxSize'])) {
                    $this->errors[] = 'File ' . $fContext['name'] . ' too many size';
                    return false;
                }
            }
        }
        return true;
    }
}