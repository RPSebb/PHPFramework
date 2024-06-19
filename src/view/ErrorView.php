<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
    <link rel="shortcut icon" href="#">
</head>
<body>
    <h1>Error <?=$error_code?></h1>
    <p><?= $errors[$error_code] ?></p>
    <p><?= $debug ?    'Code : '.$t_code    : '' ?></p>
    <p><?= $debug ? 'Message : '.$t_message : '' ?></p>
</body>
</html>