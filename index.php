<?php

// TODO 
// Auto Importer ???
// Ajouter l'authentification 
// Ajouter des sessions
// Certains champs de table doivent ils pouvoir être visible / non visible en fonction des utilisateurs ?
// Générer automatiquement des Entités à partir d'un schéma de base de données
// InputValidator faire retourner une liste d'erreurs au lieu du true / false actuel ?
// Ajouter vérification de taille dans le Validator
// Une documentation sur les différents chemins existant ainsi que leur paramètres
// Une documentation d'utilisation du framework
// Une autre gestion des erreurs ? Liste d'erreur dans un autre fichier ?
// Combiner des fichiers config .env ?
// Gérer d'autre base de données ?
// Ajouter des Globals pour les différents opérateurs, mots clés de base de données ?
// Voir InputValidator et PDODatabase
// Ajouter la possibilité d'avoir les mots clés BETWEEN, IN, LIKE
// Réfléchir à un moyen de récupérer les valeurs des FOREIGN KEY dans les controlleurs (voir ChemicalElementController)
// Une autre manière de stocker les routes ? Des arrays d'array etc ... Afin d'éviter les regex de vérifications ?
// Un autre système d'import ?

// SQL Injection : OK
// XSS : à vérifier
// CSRF : à vérifier
// Injection de commande ???
// Vulnérabilité d'upload ??
// IDOR ?

// PDODatabase si le paramètre column est vide, renvoyer tous les champs ?
// Accepter * comme column ?
// InputValidator vérifier si la saisie d'un insert est correct
// Et vérifier la saisies de plusieurs insert

$debug = true;
$errors = array(
    0    => 'Application error. Contact your administrator.',
    400  => 'Ressource not founded',
    404  => 'Among all the possibilities the Universe offers, you have found a way to end up nowhere.',
    1049 => 'Server Error.'
);

try {
    // apache_getenv();
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