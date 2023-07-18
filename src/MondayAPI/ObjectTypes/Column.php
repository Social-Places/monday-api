<?php

namespace MondayAPI\ObjectTypes;

class Column extends ObjectModel
{
    static $scope = 'columns';

    // Fields
    static $fields = array(
        'archived' => ['type' => '!Boolean',],
        'id' => ['type' => '!ID',],
        'pos' => ['type' => 'String',],
        'settings_str' => ['type' => '!String',],
        'title' => ['type' => '!String',],
        'type' => ['type' => '!String',],
        'width' => ['type' => 'Int',]
    );

    public static function newColValue($id_column, $value) {
        return $value;
    }

    public static function newColumnValues($items) {
        $column_values = array();
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $column_values[$key] = self::newColValue($key, $value);
            }s
        }
        return addslashes(json_encode($column_values));
    }

}