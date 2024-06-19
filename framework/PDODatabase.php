<?php
class PDODatabase {
    const MYSQL_ESCAPE_CHAR = '`';
    const PGSQL_ESCAPE_CHAR = '"';
    const MYSQL_CHARSET = 'charset=';
    const PGSQL_CHARSET = 'options=--client_encoding=';

    protected PDO $pdo;

    public function __construct() {
        $this->pdo = new PDO(
            constant('dbType').
            ':host='.constant('dbHost').
            ';dbname='.constant('dbName').
            ';port='.constant('dbPort').
            ';'.$this->constant('CHARSET').constant('dbCharset'),
            constant('dbUser'), constant('dbPassword'));
    }

    private function constant(string $var = '') {
        return constant('self::'.strtoupper(constant('dbType')).'_'.$var);
    }

    private function escape(string | array $value) : string {
        $char = $this->constant('ESCAPE_CHAR');
        $type = gettype($value);
        if($type === 'string') {
            return $char.str_replace(',', $char.','.$char, $value).$char;
        } else if($type === 'array') {
            return $char.implode($char.','.$char, $value).$char;
        }
    }

    // Return an array representing the data
    public function select(string $table = '', array $columns = [], string $where = '') : array {
        $table   = $this->escape($table);
        $columns = $this->escape($columns);
        $sql     = "SELECT $columns FROM $table";

        if(!empty($where)) {$sql .= " WHERE $where"; }

        $statement = $this->pdo->query($sql);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function insert(string $table = '', array $columns = [], array $values = []) : array {
        $placeholder = ':'.implode(',:', $columns);
        $table       = $this->escape($table);
        $columns     = $this->escape($columns);
        $sql         = "INSERT INTO $table ($columns) VALUES ($placeholder);";

        $stmt = $this->pdo->prepare($sql);
        $output = array('Succed' => 0, 'Failed' => 0);

        // Transform values to an array of array(key => value)
        if(!(isset($values[0]) && gettype($values[0]) === 'array')) { $values = [$values]; }

        foreach($values as $row) {
            $key = $stmt->execute($row) ? 'Succed' : 'Failed';
            $output[$key] += 1;
        }

        return $output;
    }

    public function update(string $table, array $values, string $where = 'id=-1') {
        $char = $this->constant('ESCAPE_CHAR');
        $placeholder = '';
        foreach(array_keys($values) as $key) {
            $placeholder .= "$char$key$char = :$key,";
        }

        $placeholder = rtrim($placeholder, ',');
        $table       = $this->escape($table);
        $sql         = "UPDATE $table SET $placeholder WHERE $where;";

        $stmt  = $this->pdo->prepare($sql);
        $count = $stmt->execute($values);

        return "$count line(s) updated";
    }

    public function delete(string $table = '', string $where = 'id=-1') : string {
        $table = $this->escape($table);
        $count = $this->pdo->exec("DELETE FROM $table WHERE $where;");
        return "$count line(s) deleted";
    }

    public function query(string $sql) {
        $statement = $this->pdo->query($sql);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
}
?>