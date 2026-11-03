<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\exception\LengthTouiteException;
use iutnc\touiteur\classes\Profil;

$profil=new Profil("Selena","Pe","jaimeles@gmail.com");
$utilisateur = new Utilisateur($profil, "Aidan");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Touiteur</title>
</head>
<body>

<h1>Publier un touite</h1>

<form method="POST" action="">
    <textarea name="message"
              rows="5"
              cols="30"
              maxlength="235"
              placeholder="Message ..."></textarea>

    <br>
    <input type="submit" value="Publier">
</form>

</body>
</html>
