<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\exception\LengthTouiteException;

// Création d'un utilisateur de test
$utilisateur = new Utilisateur($profil, "Aidan");

if (isset($_POST['message'])) {
    $message = trim($_POST['message']);

    if ($message !== '') {
        try {
            $touite = new Touite($message, $utilisateur,date('Y-m-d H:i:s'));

            $utilisateur->ajouterTouite($touite);

            echo "Touite publié !";

        } catch (LengthTouiteException $e) {
            echo htmlspecialchars($e->getMessage());
        }
    }
}

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
