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
        if (is_null($value))
            return '';
        return $value;
    }
}
class NumberField extends FieldType {
    public function parse($value) {
        if (is_null($value))
            return 0;
        return intval($value);
    }
}
class DateField extends FieldType {
    public function parse($value) {
        if (is_null($value))
            return Datetime::createFromFormat('!', '');
        return parse_date($value);
    }
}
class CheckboxField extends FieldType {
    public function parse($value) {
        if (is_null($value))
            return FALSE;
        return parse_boolean($value);
    }
}
class CompoundField extends FieldType {
    public function init($blueprint) {
        parent::init($blueprint);
        $this->fields = array_get($blueprint, 'fields', array());
        foreach ($this->fields as $fieldname => $subblueprint) {
            $this->fields[$fieldname] = Blueprint::createField($subblueprint);
        }
        //$this->fields = __::map(array_get($blueprint, 'fields', array()), createField);
    }
    public function parse($value) {
        if (is_null($value))
            return new ValueCollection(array());

        $result = array();
        foreach ($value as $key => $subvalue) {
            $field_type = array_get($this->fields, $key, null);
            if ($field_type)
                $result[$key] = $field_type->parse($subvalue);
        }
        foreach ($this->fields as $key => $field_type) {
            if (!array_key_exists($key, $result))
                $result[$key] = $field_type->default;
        }
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
        $result = [];
        foreach ($value as $itemvalue) {
            array_push($result, $this->item_type->parse($itemvalue));
        }
        return $result;
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
        $fieldTypeName = array_get($blueprint, 'type', null);
        $fieldType = array_get(Blueprint::$fieldTypes, $fieldTypeName, null);
        if (is_null($fieldType))
            exit('Unknown field type: '.$fieldTypeName);
        $field = new $fieldType($fieldTypeName);
        $field->init($blueprint);
        return $field;
    }

    private static function create($blueprint) {
        $result = new CompoundField('blueprint');
        $result->init($blueprint);
        return $result; 
    }

    public static function read($blueprintName) {
        $root_path = realpath(path_combine(__DIR__, '..'));
        $yaml_blueprint = Spyc::YAMLLoad(path_combine($root_path, 'blueprints', $blueprintName.'.yaml'));
        return Blueprint::create($yaml_blueprint);
    }

}

?>