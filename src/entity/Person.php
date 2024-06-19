<?php
class Person extends Entity {

    #[Column(type: 'integer', auto: true, null: false)]
    protected int $id = -1;

    #[Column(name: 'last_name', type: 'string', size: 50, null: false)]
    protected string $lastName = '';

    #[Column(type: 'string', size: 50, null: false)]
    protected string $first_name = '';

    #[Column(type: 'date', null: false)]
    protected string $date_of_birth = '';

}
?>