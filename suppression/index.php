<?php

$titre = "Suppression d'un étudiant";

// récupération des étudiants et des options
$lesEtudiants = json_encode(Etudiant::getListe());

$head = <<<HTML
<script>
    const lesEtudiants = $lesEtudiants;
</script>
HTML;

// chargement de l'interface
require RACINE . "/include/interface.php";
