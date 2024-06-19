<?php

class ChemicalElement extends Entity {
    #[Field(type: 'integer', null: false)]
    protected $atomic_number;

    #[Field(type: 'string', null: false)]
    protected $name;

    #[Field(type: 'string', null: false)]
    protected $symbol;

    #[Field(type: 'float', null: false)]
    protected $atomic_mass;

    #[Field(type: 'integer', null: true)]
    protected $family_id;

    #[Field(type: 'integer', null: true)]
    protected $block_id;

    #[Field(type: 'integer', null: true)]
    protected $state_id;

    #[Field(type: 'integer', null: true)]
    protected $abondance_id;

}

class ElementAbondance extends Entity {
    #[Field(type: 'integer', null: false)]
    protected $id;

    #[Field(type: 'string', null: false)]
    protected $name;
}

class ElementBlock extends Entity {
    #[Field(type: 'integer', null: false)]
    protected $id;

    #[Field(type: 'string', null: false)]
    protected $name;
}

class ElementFamily extends Entity {
    #[Field(type: 'integer', null: false)]
    protected $id;

    #[Field(type: 'string', null: false)]
    protected $name;
}

class ElementState extends Entity {
    #[Field(type: 'integer', null: false)]
    protected $id;

    #[Field(type: 'string', null: false)]
    protected $name;
}
?>