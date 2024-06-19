<?php
class InputValidator {
    protected string $driver;
    const MYSQL_KEYWORDS  = ['and', 'or'];
    const MYSQL_OPERATORS = ['<>', '!=', '>=', '<=', '>', '<', '='];
    const PGSQL_KEYWORDS  = ['and', 'or'];
    const PGSQL_OPERATORS = ['<>', '!=', '>=', '<=', '>', '<', '='];
    const DATE_MIN = 19000101;
    const DATE_MAX = 20240529;

    public function __construct() {
        $this->driver = strtoupper(constant('dbType'));
    }

    private function constant(string $var = '') {
        // return self::{$this->driver.'_'.$var}; // works but idk
        return constant('self::'.$this->driver.'_'.$var);
    }

    // Check if input columns are in the Entity readableColumns
    public function columnsValid(string $class, array $requestedColumns = []) : bool {
        return empty(array_diff($requestedColumns, $class::getColumnNames()));
    }

    public function valuesValid(string $class, array $values) {
        if(empty($values)) { return false; }

        // Transform array key value into an array of array key value
        // [
        //   [last_name => Edourd, first_name => Ricar ...],
        //   [last_name => Olga,   first_name => Tomi  ...]
        // ]
        if(!(isset($values[0]) && gettype($values[0]) === 'array')) { $values = [$values]; }

        foreach($values as $row) {
            foreach($row as $key => $value) {

                // Get Column Object from the column name
                $column = $class::getColumn($key);

                // Column does not exist
                if($column === null) { return false; }

                // If Column is not nullable and input value is empty
                if(!$column->isNullable() && empty($value)) { return false; }

                // If Column has a different type than the input value
                if(!$this->sameType("'$value'", $column->getType())) { return false; }
            }
        }

        return true;
    }

    public function whereValid(string $class, string $where) : bool {
        if(empty($where)) { return true; }

        $keywordsRegex  = '/('.implode('|', $this->constant('KEYWORDS' )).')/';
        $operatorsRegex = '/('.implode('|', $this->constant('OPERATORS')).')/';

        $input = preg_replace('/(\(|\))/', ' ', strtolower($where));

        $conditions = preg_split($keywordsRegex, $input);

        foreach($conditions as $condition) {
            $res = preg_split($operatorsRegex, $condition, -1, PREG_SPLIT_DELIM_CAPTURE);
            if(count($res) !== 3) { return false; }

            $key      = trim($res[0]);
            $operator = trim($res[1]); // Need for IN, to verify if its an array (method todo)
            $value    = trim($res[2]);

            $column = $class::getColumn($key);
            if($column === null) { return false; }
            // column and value not the same type
            if(!$this->sameType($value, $column->getType())) { return false; }
        }

        return true;
    }

    // Check if an input value equals to the column type
    public function sameType(string $value, string $type) : bool {
        switch ($type) {
            case 'integer':
                return $this->isInteger($value);
            case 'float':
                return is_numeric($value);
            case 'string':
                return $this->isString($value);
            case 'boolean':
                return $this->isBoolean($value);
            case 'date':
                return $this->isDate($value);
            default:
                return false;
        }
    }

    public function isBoolean(string $value) : bool {
        return preg_match('/(?<!\S)(true|false)(?!\S)/', strtolower(trim($value)));
    }

    public function isInteger(string $value) : bool {
        return preg_match('/^[0-9]+$/', $value);
    }

    public function isString(string $value) : bool {
        if($valueSize = strlen($value) < 2) { return false; }

        $startEnd = $value[0].$value[$valueSize - 1];
        if( ($startEnd === '""' && preg_match_all('/(?<!\\\\)"/', $value) === 2) ||
            ($startEnd === "''" && preg_match_all("/(?<!\\\\)'/", $value) === 2)) {
            return true;
        }

        return false;
    }

    public function isDate(string $value) : bool {
        if(strlen($value) != 12) { return false; }

        // Match format y-mm-dd surrounded by single or double quote
        // Year can be -x to +x, but need to have a value
        // if(preg_match_all('/^(\'|\")(-?\d+)-(\d{2})-(\d{2})\1$/', $value, $matches, PREG_SPLIT_NO_EMPTY)) {

        // Match format yyyy-mm-dd
        if(preg_match_all('/^(\'|\")(\d{4})-(\d{2})-(\d{2})\1$/', $value, $matches, PREG_SPLIT_NO_EMPTY)) {
            $year  = intval($matches[2][0]);
            $month = intval($matches[3][0]);
            $day   = intval($matches[4][0]);
            $maxDay = 31;

            if($month === 2) {
                $maxDay = $maxDay - 3 + (int)$this->isBissextile($year);
            } else if(in_array($month, [4, 6, 9, 11])) {
                $maxDay = $maxDay - 1;
            }

            if($this->inRange(1, 12, $month) && $this->inRange(1, $maxDay, $day)) {
                return true;
            }

            // For insert or update, check if date is within certain range
            // if(!$this->inRange($this->constant('DATE_MIN'), $this->constant('DATE_MAX'), ($year * 10000) + ($month * 100) + $day)) {
            //     return false;
            // };
        };

        return false;
    }

    // Check if value is withing range, inclusive bonds
    private function inRange(int $min, int $max, int $value) : bool {
        return $value >= $min && $value <= $max;
    }

    function isBissextile(int $nb) : bool {
        return ($nb % 4 == 0 && $nb % 100 != 0) || ($nb % 400 == 0);
    }

}
?>