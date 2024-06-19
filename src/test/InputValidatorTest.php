<?php
class InputValidatorTest extends AbstractTest {

    public function isBooleanTest() {
        $method = 'isBoolean';

        $this->assert($method,                         'True',  true, ['True' ]);
        $this->assert($method,                         'true',  true, ['true' ]);
        $this->assert($method,                         'TRUE',  true, ['TRUE' ]);
        $this->assert($method,                        'False',  true, ['False']);
        $this->assert($method,                        'false',  true, ['false']);
        $this->assert($method,                        'FALSE',  true, ['FALSE']);
        $this->assert($method,               'string boolean', false, ['"FALSE"']);
        $this->assert($method,               'string boolean', false, ['\'FALSE']);
        $this->assert($method,                   'int around', false, ['1FALSE1']);
        $this->assert($method,                  'Added space',  true, ['   FALSE            ']);
        $this->assert($method,             'In bewteen space', false, ['   F A L  SE            ']);
        $this->assert($method,                  'Wrong value', false, ['  aze']);
        $this->assert($method,                  'Empty value', false, ['']);
    }

    public function columnsValidTest() {
        $allowedColumns = ['1', '2', '3', '4'];
        $method = 'columnsValid';

        $this->assert($method, 'One bad value'               , false, [$allowedColumns, 'a']);
        $this->assert($method, 'Multiple bad values'         , false, [$allowedColumns, ' aze,  dzasd ,  zad']);
        $this->assert($method, 'Empty'                       , false, [$allowedColumns, '']);
        $this->assert($method, 'One good value'              , true , [$allowedColumns, '2']);
        $this->assert($method, 'Multiple good value'         , true , [$allowedColumns, ' 1,  2,  3']);
    }

    public function whereValidTest() {
        $entity = Person::class;
        $method = 'whereValid';

        $this->assert($method, 'Wrong type'                  , false, [$entity, 'last_name = 1']);
        $this->assert($method, 'Wrong column'                , false, [$entity, 'lat_name = "Olga"']);
        $this->assert($method, 'Good type and column'        , true , [$entity, 'last_name = "Olga"']);
        $this->assert($method, 'Injection column name'       , false, [$entity, 'last_name" UNION SELECT 1, 2, 3 -- ']);
        $this->assert($method, 'Injection numerical value'   , false, [$entity, 'id=1 OR 1=1']);
        $this->assert($method, 'Injection with operator'     , false, [$entity, 'id=1 && 1=1']);
        $this->assert($method, 'Injection subquery'          , false, [$entity, 'id=(SELECT id FROM users WHERE username="admin")']);
        $this->assert($method, 'Injection comment'           , false, [$entity, 'id=1 /* comment */ OR 1=1")']);
        $this->assert($method, 'Injection comment'           , false, [$entity, 'id = 1/*']);
        $this->assert($method, 'Injection comment'           , false, [$entity, 'id = 1; --']);
        $this->assert($method, 'Injection comment'           , false, [$entity, 'id = 1" --']);
        $this->assert($method, 'Injection comment'           , false, [$entity, 'id = 1"/*']);
        $this->assert($method, 'Injection comment'           , false, [$entity, 'id = 1\'/*']);
        $this->assert($method, 'Injection comment'           , false, [$entity, 'id = "1; DROP TABLE person_test"']);
        $this->assert($method, 'Injection comment drop table', false, [$entity, 'id = 1; DROP TABLE users; --']);
        $this->assert($method, 'Injection excess space'      , false, [$entity, 'id =   1 OR   1 = 1']);
        $this->assert($method, 'Injection special char'      , false, [$entity, 'id=1 XOR 1=1']);
        $this->assert($method, 'Empty \"\"'                  , false, [$entity, ""]);
        $this->assert($method, 'Empty \'\''                  , false, [$entity, '']);
        $this->assert($method, 'Mutiple Numbers'             , false, [$entity, 'id = 1215451 511212']);
        $this->assert($method, 'Union attack'                , false, [$entity, 'id = 1 UNION SELECT username, password FROM users']);
        $this->assert($method, 'Logic operator'              , false, [$entity, 'id = 1 OR 1=1']);
        $this->assert($method, 'Column value both quote'     , true , [$entity, 'first_name = "Gerard" and last_name = \'zamal\'']);
        $this->assert($method, 'Weird parenthesis'           , true , [$entity, '(id =(1 and last_name) ="za")()(']);
        $this->assert($method, 'Single Number'               , true , [$entity, 'id = 1215451511212']);
        $this->assert($method, 'Ok conditons'                , true , [$entity, 'last_name = "Olga" or id = 4']);
        $this->assert($method, 'SQL map #1'                  , false, [$entity, "last_name='Olga' UNION ALL SELECT NULL,CONCAT(0x7176787871,0x49504a5375536b5a70434354424d70536f5157564a6345434a454a4345544944476f4c5a4848614a,0x716a627671),NULL,NULL-- -"]);
    }
}
?>