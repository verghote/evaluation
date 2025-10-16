<?php
// Contrôle de l'existence du paramètre attendu : id
if (!isset($_POST['id'])) {
    Erreur::envoyerReponse("Paramètre manquant", 'global');
}

$id = $_POST['id'];

// vérification de l'existence de l'étudiant
$ligne = Etudiant::getById($id);
if (!$ligne) {
    Erreur::envoyerReponse("Cet étudiant n'existe pas", 'global');
}

// suppression de l'enregistrement en base de données
Etudiant::supprimer($id);

// suppression du fichier image associé
if (!empty($ligne['photo'])) {
    Etudiant::supprimerPhoto($ligne['photo']);
}

$reponse = ['success' => "L'étudiant a été supprimé"];
echo json_encode($reponse, JSON_UNESCAPED_UNICODE);
