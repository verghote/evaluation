<?php
// alimentation de l'interface
$titre = "Modification des  informations signalétiques d'un étudiant";

// Récupération de la liste des Etudiants : id, nomPrenom
$lesEtudiants= json_encode(Etudiant::getListe());

// un étudiant doit avoir entre 17 et 25 ans
$dateMax = json_encode(date('Y') - 17 . '-01-01');
$dateMin = json_encode(date('Y') - 25 . '-01-01');

$head = <<<HTML
    <script src="/composant/autocomplete/autocomplete.min.js"></script>
    <link rel="stylesheet" href="/composant/autocomplete/autocomplete.css">
    <script>
        const dateMin = $dateMin;
        const dateMax = $dateMax;
        const lesEtudiants = $lesEtudiants;
</script>
HTML;

// chargement de l'interface
require RACINE . "/include/interface.php";