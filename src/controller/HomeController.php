<?php
class HomeController {

    #[Route('/', httpMethods: ['GET'])]
    public function read() {
        http_response_code(200);
        include_once(__DIR__ .'/../View/Base.php');
    }
}
?>