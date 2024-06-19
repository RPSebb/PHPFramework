<?php
class TestController {

    #[Route('/test/all', httpMethods: ['GET'])]
    public function read() {
        echo('get all test');
    }

    #[Route('/test/one', httpMethods: ['GET'])]
    public function readOne() {
        echo('get one test');
    }

    #[Route('/test/create', httpMethods: ['POST'])]
    public function create() {
        echo('create one test');
    }
}
?>