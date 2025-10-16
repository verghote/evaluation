<?php
// alimentation de l'interface :
$titre = "Photo des étudiants";

// récupération des étudiants
$lesEtudiants = json_encode(Etudiant::getAll());

// récupération des paramètres de configuration pour la photo des étudiants
$lesParametres = json_encode(Etudiant::getConfig());

$head = <<<EOD
    <script>
        const lesEtudiants = $lesEtudiants;
        const lesParametres = $lesParametres;
    </script>
EOD;

require RACINE . "/include/interface.php";

