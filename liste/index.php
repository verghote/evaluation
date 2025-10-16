<?php
// alimentation de l'interface :
$titre = "Liste des étudiants ";

// récupération des étudiants
$data = json_encode(Etudiant::getAll());

$head = <<<EOD
<script>
    let data = $data;
</script>
EOD;

// chargement de l'interface
require RACINE . "/include/interface.php";
