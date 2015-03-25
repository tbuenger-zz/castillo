<?php

require_once 'utils.php';
require_once 'core.php';

abstract class FieldType {

    public function __construct($type) {
        $this->type = $type;
    }

    abstract public function parse($value);

    public function init($blueprint) {
        $this->label = array_get($blueprint, 'label', '');
        $this->description = array_get($blueprint, 'description', '');
        $this->default = $this->parse(array_get($blueprint, 'default', null));
    }

}

class TextField extends FieldType {
    public function parse($value) {
        return is_null($value) ? '' : $value;
    }
}

class NumberField extends FieldType {
    public function parse($value) {
        return is_null($value) ? 0 : intval($value);
    }
}

class DateField extends FieldType {
    public function parse($value) {
        return is_null($value) ? Datetime::createFromFormat('!', '') : parse_date($value);
    }
}

class CheckboxField extends FieldType {
    public function parse($value) {
        return is_null($value) ? FALSE : parse_boolean($value);
    }
}

class CompoundField extends FieldType {

    public function init($blueprint) {
        parent::init($blueprint);
        $this->fields = array_get($blueprint, 'fields', array());
        foreach ($this->fields as $fieldname => &$subblueprint)
            $subblueprint = Blueprint::createField($subblueprint);
    }

    public function parse($value) {
        if (is_null($value))
            return new ValueCollection();

        $result = array();
        foreach ($this->fields as $key => $field_type)
            $result[$key] = $field_type->default;

        foreach ($value as $key => $subvalue)
            if ($field_type = array_get($this->fields, $key, null))
                $result[$key] = $field_type->parse($subvalue);

        return new ValueCollection($result);
    }

}

class ListField extends FieldType {

    public function init($blueprint) {
        parent::init($blueprint);
        $this->item_type = new CompoundField('item');
        $this->item_type->init($blueprint);
    }

    public function parse($value) {
        if (is_null($value))
            return [];
        return __::map($value, function($x){return $this->item_type->parse($x);});
    }

}

abstract class Blueprint {

    private static $fieldTypes = array(
        'text' => 'TextField',
        'date' => 'DateField',
        'checkbox' => 'CheckboxField',
        'number' => 'NumberField',
        'list' => 'ListField',
        'selectbox' => 'TextField',
        'url' => 'TextField',
        'textarea' => 'TextField',
        'mail' => 'TextField',
        'info' => 'TextField',
        );

    public static function createField($blueprint) {
        $typename = array_get($blueprint, 'type', null);
        $type = array_get(Blueprint::$fieldTypes, $typename, null);
        if (is_null($type))
            exit('Unknown field type: '.$typename);
        $field = new $type($typename);
        $field->init($blueprint);
        return $field;
    }

    private static function create($blueprint) {
        $result = new CompoundField('blueprint');
        $result->init($blueprint);
        return $result; 
    }

    public static function read($blueprintName) {
        $yaml_blueprint = Spyc::YAMLLoad(path_combine(Paths::$blueprints, $blueprintName.'.yaml'));
        return Blueprint::create($yaml_blueprint);
    }

}

?>