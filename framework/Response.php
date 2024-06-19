<?php
class Response {

    public function __construct(string $content, int $code, mixed $data, string $charset = 'utf-8') {
        header("Content-Type: $content ; charset= $charset");
        http_response_code($code);
        echo($data);
    }
}
?>