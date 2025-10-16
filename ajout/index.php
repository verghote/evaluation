<?php
$titre = "Ajout d'un étudiant";

// récupération des paramètres de configuration pour la photo des étudiants
$lesParametres = json_encode(Etudiant::getConfig());

// récupérer les options pour alimenter la zone de liste des options
$lesOptions = json_encode(Etudiant::getLesOptions());

// un étudiant doit avoir entre 17 et 25 ans
$dateMax = date('Y') - 17 . '-01-01';
$dateMin = json_encode(date('Y') - 25 . '-01-01');


$head = <<<HTML
<script>
    const lesOptions = $lesOptions;
    const dateMin = $dateMin;
    const dateMax = '$dateMax';
    const lesParametres = $lesParametres;
</script>
HTML;

// chargement de l'interface
require RACINE . "/include/interface.php";
