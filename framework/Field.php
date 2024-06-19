<?php
/** Represent a column from the database
 * 
 * Must be use as an attribute on an Entity property
 */
#[Attribute]
class Column implements Stringable {

    /** Name of the column.
     * 
     * Must be the same as in database.
     * 
     * Alternatively, you can leave this empty and name the property exactly the same as in database.
     */
    protected string $name;
    protected string $type;
    protected int $size;
    protected bool $auto, $null;

    public function __construct(
        string $name  = '',
        string $type  = '',
        int    $size  = -1,
        bool   $auto  = false,
        bool   $null  = false
    ) {
        $this->name  = $name;
        $this->type  = $type;
        $this->size  = $size;
        $this->auto  = $auto;
        $this->null  = $null;
    }

    public function getName()    : string { return $this->name; }
    public function getType()    : string { return $this->type; }
    public function getSize()    : int    { return $this->size; }
    public function isAuto()     : bool   { return $this->auto; }
    public function isNullable() : bool   { return $this->null; }

    public function __toString() {
        return (
            'name: '. $this->name.', '.
            'type: '. $this->type.', '.
            'size: '. $this->size.', '.
            'auto: '.($this->auto  ? 'True' : 'False').', '.
            'null: '.($this->null  ? 'True' : 'False')
        );
    }
}
?>