<?php
// alimentation de l'interface :
$titre = "Trombinoscope ";



$head = <<<EOD
<script>
    let lesEtudiants = $lesEtudiants;
</script>
EOD;

// chargement de l'interface
require RACINE . "/include/interface.php";
