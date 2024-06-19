<?php
abstract class Entity {
    protected static $columns;
    protected static $notNullColumns;

    /** Return a Column from a name. Return null on fail
     * 
     * @exemple
     * 
     * class Person extends Entity {
     *
     * #[Column(type: 'integer', auto: true, null: false)]
     * 
     * protected int $id = -1;
     *
     * #[Column(type: 'string', size: 50, null: false)]
     * 
     * protected string $last_name = '';
     *
     * #[Column(type: 'string', size: 50, null: false)]
     * 
     * protected string $first_name = '';
     *
     * #[Column(type: 'date', null: false)]
     * 
     * protected string $date_of_birth = '';
     *
     * }
     * 
     * Person::getColumnAttribute('last_name')
     * 
     * * return : Column(type: 'string', size: 50, null: false)
     * 
     * Person::getColumnAttribute('contact')
     * 
     * * return : null
     */
    public static function getColumn(string $name) : ?Column {
        self::storeColumns();
        if(isset(self::$columns[$name])) { return self::$columns[$name]; }
        return null;
    }

    /** List of all columns names */
    public static function getColumnNames() : array {
        self::storeColumns();
        return array_keys(self::$columns);
    }

    /** List of all Columns where null = false */
    public static function getNotNullableColumns() : array {
        self::storeColumns();
        return self::$notNullColumns;
    }

    protected static function storeColumns() {
        if(isset(self::$columns)) { return; }
        $reflection = new ReflectionClass(static::class);
        foreach($reflection->getProperties() as $property) {
            foreach($property->getAttributes('Column') as $attribute) {
                $instance = $attribute->newInstance();
                if(empty($name = $instance->getName())) { $name = $property->getName(); }
                self::$columns[$name] = $instance;
                if(!$instance->isNullable() && !$instance->isAuto()) { self::$notNullColumns[] = $name; }
            }
        }
    }
}
?>