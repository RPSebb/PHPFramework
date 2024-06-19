<?php

class ChemicalElement extends Entity {
    #[Column(type: 'integer', null: false)]
    protected $atomic_number;

    #[Column(type: 'string', null: false)]
    protected $name;

    #[Column(type: 'string', null: false)]
    protected $symbol;

    #[Column(type: 'float', null: false)]
    protected $atomic_mass;

    #[Column(type: 'integer', null: true)]
    protected $family_id;

    #[Column(type: 'integer', null: true)]
    protected $block_id;

    #[Column(type: 'integer', null: true)]
    protected $state_id;

    #[Column(type: 'integer', null: true)]
    protected $abondance_id;

}

class ElementAbondance extends Entity {
    #[Column(type: 'integer', null: false)]
    protected $id;

    #[Column(type: 'string', null: false)]
    protected $name;
}

class ElementBlock extends Entity {
    #[Column(type: 'integer', null: false)]
    protected $id;

    #[Column(type: 'string', null: false)]
    protected $name;
}

class ElementFamily extends Entity {
    #[Column(type: 'integer', null: false)]
    protected $id;

    #[Column(type: 'string', null: false)]
    protected $name;
}

class ElementState extends Entity {
    #[Column(type: 'integer', null: false)]
    protected $id;

    #[Column(type: 'string', null: false)]
    protected $name;
}
?>
