<?php

namespace Models;

class DataType
{
    const STRING = 'string';
    const INTEGER = 'integer';
    const FLOAT = 'float';
    const OBJECT = 'object';
    const ARRAY = 'array';

    public static function isDataType($dataType, $data)
    {
        switch($dataType) {
            case self::STRING:
                return self::isString($data);
                break;
            case self::INTEGER:
                return self::isInteger($data);
                break;
            case self::FLOAT:
                return self::isFloat($data);
            case self::OBJECT:
                return self::isObject($data);
            case self::ARRAY:
                return self::isArray($data);
                break;
            default:
                die('Uh oh no... This is not a valid DT');
                break;
        }
    }

    /**
     * Returns true if the data type equals String.
     * @param $data
     * @return bool
     */
    private static function isString($data)
    {
        if (is_string($data)) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if the data type equals Integer.
     * @param $data
     * @return bool
     */
    private static function isInteger($data)
    {
        if (is_int($data)) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if the data type equals Float.
     * @param $data
     * @return bool
     */
    private static function isFloat($data)
    {
        if (is_float($data)) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if the data type equals Object.
     * @param $data
     * @return bool
     */
    private static function isObject($data)
    {
        if (is_object($data)) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if the data type equals Array.
     * @param $data
     * @return bool
     */
    private static function isArray($data)
    {
        if (is_array($data)) {
            return true;
        }
        return false;
    }


}