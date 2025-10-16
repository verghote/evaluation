<?php
// activation du chargement dynamique des ressources
// require $_SERVER['DOCUMENT_ROOT'] . "/include/autoload.php";

// alimentation de l'interface
$titre = "Recherche sur le nom et prénom";

// Récupération de la liste des Etudiants : licence, nom et prenom
$lesEtudiants= json_encode(Etudiant::getListe());

// chargement tarekraafat-autocomplete.js
//Simple autocomplete pure vanilla Javascript library.
$head = <<<HTML
    <script src="/composant/autocomplete/autocomplete.min.js"></script>
    <link rel="stylesheet" href="/composant/autocomplete/autocomplete.css">
    <script>
        const lesEtudiants = $lesEtudiants;
    </script>
HTML;

// chargement de l'interface
require RACINE . "/include/interface.php";
