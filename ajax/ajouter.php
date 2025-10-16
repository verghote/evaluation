<?php
// Contrôle sur le nom de la classe
if (!isset($_POST['table']) || !class_exists($_POST['table'])) {
    Erreur::envoyerReponse("La table n'est pas transmise ou n'existe pas.", 'global');
}

// récupération des données
$table = $_POST['table'];

// création d'une instanciation dynamique de classe
$table = new $table();
// Ajout dans la table en vérifiant que tous les champs sont corrects
if (!$table->donneesTransmises()) {
    $reponse = ['error' => $table->getLesErreurs()];
} elseif (!$table->checkAll()) {
    $reponse = ['error' => $table->getLesErreurs()];
} else {
    $table->insert();
    if ($table->getLastInsertId()) {
        $reponse = ['success' => $table->getLastInsertId()];
    } else {
        $reponse = ['success' => 'Opération réalisée avec succès'];
    }
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($reponse, JSON_UNESCAPED_UNICODE);
