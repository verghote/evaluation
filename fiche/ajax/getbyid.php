<?php
// require $_SERVER['DOCUMENT_ROOT'] . "/include/autoload.php";

// vérification de la transmission du paramètre attendu : id
if (!isset($_POST['id'])) {
    Erreur::envoyerReponse("Numéro de l'étudiant non transmis", 'id');
}

// Récupération de l'étudiant
$id = $_POST['id'];

// vérification du format de l'id
if (!preg_match('/^[0-9]+$/', $id)) {
    Erreur::envoyerReponse("Numéro de l'étudiant non conforme", 'id');
}

// Récupération de l'étudiant
$ligne = Etudiant::getById($id);
// Si l'étudiant n'existe pas, on envoie une erreur
if (!$ligne) {
    Erreur::envoyerReponse("Cet étudiant n'existe pas", 'id');
}

// envoi de la réponse
echo json_encode($ligne);
