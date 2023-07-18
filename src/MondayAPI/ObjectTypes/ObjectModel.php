<?php

namespace MondayAPI\ObjectTypes;

use MondayAPI\Querying\Query;

class ObjectModel
{
    // Query scope
    static $scope = '';

    // Arguments
    static $arguments = array();

    // Fields
    static $fields = array();

    function __construct() {
        return $this;
    }

    public function getFields(array $fields = [], $alt_fields = false) {
        return [Query::buildFields(
            Query::buildFieldsArgs(
                ($alt_fields == false ? static::$fields : $alt_fields),
                $fields
            )
        )];
    }

    public function getArguments(array $arguments = [], $alt_arguments = false, String $prepend_args = '') {
        return Query::buildArguments(
            Query::buildArgsFields(
                ($alt_arguments == false ? static::$arguments : $alt_arguments),
                $arguments
            ),
            $prepend_args
        );
    }

    public function getBuildFieldsArgs() {
        //return '{ ... }';
        return false;
    }
}