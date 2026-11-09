<?php
declare(strict_types=1);

use ClasseTechnique\InterfaceSystem;
use ClasseMetier\Etudiant;
use ClasseMetier\Pdf;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// chargement des données
$lesLignes = Etudiant::getAll();

if (count($lesLignes) === 0) {
    $titre = "Liste des étudiants";
    $message = "Aucun étudiant n'a été trouvé pour le moment.";
    $type = "avertissement";
    $interface = new InterfaceSystem($titre, $message, $type);
    $interface->afficher();
}

// En-tête du tableau
$corps = <<<HTML
<table >
    <thead> 
        <tr>
            <th style="width: 30%;">Nom</th>
            <th style="width: 30%;">Prénom</th>
            <th style="width: 8%;" class="center">Sexe</th>
            <th style="width: 16%;" class="center">Date de naissance</th>
            <th style="width: 16%;" class="center">Option</th>
        </tr>
    </thead>
    <tbody>
HTML;

// Corps du tableau
foreach ($lesLignes as $ligne) {
    $nom        = $ligne['nom'];
    $prenom     = $ligne['prenom'];
    $sexe       = $ligne['sexe'];
    $dateNaissance    = $ligne['dateNaissanceFr'];
    $option   = $ligne['libelleCourt'];

    $corps .= <<<ROW
        <tr>
            <td>$nom</td>
            <td>$prenom</td>
            <td class="center">$sexe</td>
            <td class="center">$dateNaissance</td>
            <td class="center">$option</td>
        </tr>
ROW;
}

$corps.= <<<HTML
    </tbody>
</table>
HTML;

// génération du PDF
Pdf::genererDocument($corps, "Liste des étudiants", "liste.pdf");

