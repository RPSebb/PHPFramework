<?php
$debug = true;
$errors = array(
    0    => 'Application error. Contact your administrator.',
    400  => 'Ressource not found.',
    404  => 'Oops. Page not found.',
    1049 => 'Server Error.'
);

try {
    require_once(__DIR__ .'/framework/Importer.php');
    $importer = new Importer();
    $importer->import(__DIR__ . '/framework');
    $importer->import(__DIR__ . '/src/entity');
    $importer->import(__DIR__ . '/src/test');

    $config   = new Configuration();
    $instancier = new Instancier();
    $router = $instancier->new('Router');

} catch(Throwable $t) {
    $error_code = 0;
    $t_code = $t->getCode();
    $t_message = $t->getMessage();

    if(array_key_exists($t->getCode(), $errors)) {
        $error_code = $t->getCode();
    };

    http_response_code($t_code);
    include_once(__DIR__ . '/src/view/ErrorView.php');
}
?>
