<?php

require_once 'utils.php';
require_once 'core.php';

abstract class FieldType {
    public function __construct($name) {
        $this->name = $name;
    }
    abstract public function parse($value);
}

class TextField extends FieldType {}
class NumberField extends FieldType {}
class DateField extends FieldType {}
class CheckboxField extends FieldType {}
class ListField extends FieldType {}

$fieldTypes = array(
    'text' => TextField,
    'date' => DateField,
    'checkbox' => CheckboxField,
    'number' => NumberField,
    'list' => ListField,
    'selectbox' => TextField,
    'url' => TextField,
    'textarea' => TextField,
    'mail' => TextField,
    'info' => TextField,
    )

function createField($fieldName) {
    $fieldType = array_get($fieldTypes, $fieldName, null);
    if (!$fieldType)
        exit('Unknown field type: '.$fieldName)
    return new $fieldType($fieldName);
}

class Field {
    # name
    # type
    # description
}

class Blueprint {
    # fields = [...]
}

?>