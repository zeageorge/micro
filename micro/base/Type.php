<?php /** MicroType */

namespace Micro\base;

/**
 * Class Type
 * @package Micro
 * @subpackage base
 */
class Type {

    /**
     * Return concrete object type
     *
     * @access public
     *
     * @param mixed $var object to scan
     *
     * @return string
     * @static
     */
    public static function getType($var)
    {
        $type = gettype($var);
        switch ($type) {
            case 'object': {
                return get_class($var); break;
            }
            case 'double': {
                return is_float($var) ? 'float' : 'double'; break;
            }
            default: {
                return strtolower($type);
            }
        }
    }

    /**
     * Get public vars into object
     *
     * @access public
     *
     * @param mixed $object
     *
     * @return array
     */
    public static function getVars($object)
    {
        return get_object_vars($object);
    }
}