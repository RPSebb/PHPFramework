<?php
class PersonController {

    //---------------- READ ALL --------------------//
    #[Route('/person', httpMethods: ['GET'])]
    public function readAll(PDODatabase $db, Request $request, InputValidator $validator) : Response {
        $columns = $request->getColumns();
        $where   = $request->get('where');

        if(empty($columns)) { $columns = Person::getColumnNames(); }
        else if(!$validator->columnsValid(Person::class, $columns)) {
            return new Response('application/json', 500, json_encode('Error - allowed columns are : ' . implode(', ', Person::getColumnNames()), JSON_UNESCAPED_UNICODE));
        }

        if(!empty($where) && !$validator->whereValid(Person::class, $where)) {
            return new Response('application/json', 500, json_encode('Error - invalid where statement :' . $where, JSON_UNESCAPED_UNICODE));
        }

        $data = $db->select('person', $columns, $where);
        return new Response('application/json', 200, json_encode($data));
    }

    //---------------- READ ONE --------------------//
    #[Route('/person/{id}', httpMethods: ['GET'])]
    public function read(PDODatabase $db, Request $request, InputValidator $validator, $id) : Response {
        $columns = $request->getColumns();
        if(empty($columns)) { $columns = Person::getColumnNames(); }
        else if(!$validator->columnsValid(Person::class, $columns)) {
            return new Response('application/json', 500, json_encode('Error - allowed columns are : ' . implode(', ', Person::getColumnNames()), JSON_UNESCAPED_UNICODE));
        }

        $data = $db->select('person', $columns, "id=$id");
        return new Response('application/json', 200, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    //---------------- CREATE --------------------//
    #[Route('/person', httpMethods: ['POST'])]
    public function create(PDODatabase $db, Request $request, InputValidator $validator) : Response {
        $columns = Person::getNotNullableColumns();
        $requestData = $request->postData();
        if(!$validator->valuesValid(Person::class, $requestData)) {
            return new Response('application/json', 500, '{"Error": "invalid columns or values type"}');
        }

        $data = $db->insert('person', $columns, $requestData);
        return new Response('application/json', 200, json_encode($data));
    }

    //---------------- UPDATE  --------------------//
    #[Route('/person/{id}', httpMethods: ['PUT', 'PATCH'])]
    public function update(PDODatabase $db, Request $request, InputValidator $validator, $id) {
        if(empty($requestData = $request->formUrlEncoded()) && empty($requestData = $request->formData()) && empty($requestData = $request->formBody())) { 
            return new Response('application/json', 500, json_encode('No input values'));
        }

        $notSingleValue = isset($requestData[0]) && gettype($requestData[0]) === 'array';

        if($notSingleValue) {
            return new Response('application/json', 500, '{
                "Error": "bad data structure",
                "Expected": {"column_1":"value", "column_2":"value"} 
            }');
        }

        if(!$validator->sameType($id, Person::getColumn('id')->getType())) {
            return new Response('application/json', 500, '{"Error": "invalid id type"}');
        };

        $idDoNotExist = empty($db->select('person', ['id'], "id=$id"));

        if($idDoNotExist) {
            return new Response('application/json', 500, '{"Error": "id not found"}');
        }

        if(!$validator->valuesValid(Person::class, $requestData)) {
            return new Response('application/json', 500, '{"Error": "invalid columns or values type"}');
        }

        $data = $db->update('person', $requestData, "id=$id");
        return new Response('application/json', 200, json_encode($data));
    }

    #[Route('/person/{id}', httpMethods: ['DELETE'])]
    public function delete(PDODatabase $db, InputValidator $validator, $id = -1) {
        if(!$validator->sameType($id, Person::getColumn('id')->getType())) {
            return new Response('application/json', 500, '{"Error": "invalid id type"}');
        };

        $data = $db->delete('person', "id=$id");
        return new Response('application/json', 200, json_encode($data));
    }
}
?>