<?php

namespace Models;

/*
 * Base model which all models will extend to keep similar functionality in one place.
 */
class Model
{
    /**
     * Model constructor.
     * @param $modelData
     */
    public function __construct(...$modelData)
    {
        $modelKeys = array_keys(get_class_vars(get_class($this)));
        $modelData = array_combine($modelKeys, $modelData);
        $this->populate($modelData, \get_class($this));
    }

    /**
     * Populates a Model using the defined variables on the extended Object.
     * @param $modelData
     * @param $class
     */
    public function populate($modelData, $class)
    {
        if (!empty($modelData)) {
            foreach ($modelData as $key => $value) {
                if (array_key_exists($key, get_class_vars($class))) {
                    $this->$key = $value; // Assign the object key to the value of the array.
                } elseif (array_key_exists(ucwords($key), get_class_vars($class))) {
                    $objectName = "\Models\\" . ucwords($key); // Create long string of the model name
                    $modelName = ucwords($key);
                    $this->$modelName = new $objectName($value); // Attempt to create the object and Assign it against the key.
                }
            }
        }
    }
}