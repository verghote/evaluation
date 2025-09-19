<?php
// alimentation de l'interface :
$titre = "Trombinoscope ";

// récupération des étudiants
$lesEtudiants = json_encode(Etudiant::getAll());

$head = <<<EOD
<script>
    let lesEtudiants = $lesEtudiants;
</script>
EOD;

// chargement de l'interface
require RACINE . "/include/interface.php";
