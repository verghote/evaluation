<?php

$titre = "Modification de l'option";

// récupération des étudiants et des options
$lesEtudiants = json_encode(Etudiant::getLesEtudiants());
$lesOptions = json_encode(Etudiant::getLesOptions());

$head = <<<HTML
<script>
    const lesEtudiants = $lesEtudiants;
    const lesOptions = $lesOptions;
</script>
HTML;

// chargement de l'interface
require RACINE . "/include/interface.php";
