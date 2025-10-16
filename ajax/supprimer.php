<?php
// vérification de la transmission des données attendues
if (!Std::existe('table', 'id') ) {
    Erreur::envoyerReponse('Tous les paramètres attendus ne sont pas transmis', 'global');
}

// récupération des données
$id = $_POST["id"];
$table = $_POST['table'];

// Contrôle sur le nom de la classe
if (! class_exists($table)) {
    Erreur::envoyerReponse("La classe $table n'existe pas.", 'global');
}

// Réalisation de la suppression
$table = new $table();
$table->delete($_POST['id']);
echo json_encode(['success' => "Opération réalisée avec succès"]);
