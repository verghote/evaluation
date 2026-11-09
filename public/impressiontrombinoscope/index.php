<?php
declare(strict_types=1);

use ClasseTechnique\InterfaceSystem;
use ClasseMetier\Etudiant;
use ClasseMetier\Pdf;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Chargement des étudiants

$lesLignes = Etudiant::getAll();

if (count($lesLignes) === 0) {
    $interface = new InterfaceSystem(
        'Liste des étudiants',
        'Aucun étudiant n\'a été trouvé pour le moment.',
        'avertissement'
    );

    $interface->afficher();
}

// Initialisation du document HTML

$nombreColonnes = 8;
$colonne = 0;

$corps = <<<HTML
<style>
    table.trombinoscope {
        width: 100%;
        border-collapse: separate;
        border-spacing: 1mm;
        table-layout: fixed;
    }

    table.trombinoscope td {
        width: 12.5%;
        border: 0.3mm solid #cccccc;
        padding: 0.5mm;
        text-align: center;
        vertical-align: top;
    }

    table.fiche-etudiant {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    table.fiche-etudiant td {
        width: 100%;
        border: none;
        padding: 0;
        text-align: center;
        vertical-align: middle;
    }

    .ligne-nom {
        height: 4mm;
        line-height: 4mm;
        font-size: 6.5pt;
        font-weight: bold;
        color: #263746;
        white-space: nowrap;
    }

    .ligne-prenom {
        height: 4mm;
        line-height: 4mm;
        font-size: 6.5pt;
        font-weight: bold;
        color: #263746;
        white-space: nowrap;
    }

    .ligne-photo {
        height: 20mm;
        text-align: center;
        vertical-align: middle;
    }

    .ligne-photo img {
        width: 20mm;
        height: 20mm;
    }
</style>

<table class="trombinoscope">
<tbody>
HTML;

/**
 * Génération du trombinoscope
 * Le script génère un tableau <table> contenant la totalité des étudiants sur des lignes <tr> successives de 8 colonnes.
 * c'est le moteur PDF qui remplit la page jusqu'au bord inférieur et créer lui-même un saut de page
 * dès qu'il manque d'espace vertical (Gestion physique / automatique).
 */

foreach ($lesLignes as $ligne) {
    // Ouverture d'une nouvelle ligne toutes les 8 fiches
    if ($colonne === 0) {
        $corps .= '<tr>';
    }

    // Sélection de la photographie

    $nomPhoto = $ligne['present'] ? $ligne['photo'] : '0.png';
    $photo = DOSSIER_PHOTO_ETUDIANT ."/" . $nomPhoto;

    // Construction de la fiche

    $corps .= <<<HTML
        <td>
            <table class="fiche-etudiant">
                <tbody>
                    <tr>
                        <td class="ligne-nom">NOM_ETUDIANT</td>
                    </tr>
                    <tr>
                        <td class="ligne-prenom">PRENOM_ETUDIANT</td>
                    </tr>
                    <tr>
                        <td class="ligne-photo">
                            <img src="PHOTO_ETUDIANT" width="94" height="94" alt="Photo">
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
HTML;

    // Remplacement des placeholders par les valeurs réelles
    $corps = str_replace(['NOM_ETUDIANT', 'PRENOM_ETUDIANT', 'PHOTO_ETUDIANT'], [$ligne['nom'], $ligne['prenom'], $photo], $corps  );

    // Incrément du nombre de fiches dans la ligne
    $colonne++;

    // Fermeture après 8 étudiants
    if ($colonne === $nombreColonnes) {
        $corps .= '</tr>';
        $colonne = 0;
    }
}

// fermeture de la dernière ligne si nécessaire
if ($colonne !== 0) {
    $corps .= '</tr>';
}

// Fermeture du tableau HTML
$corps .= <<<HTML
        </tbody>
    </table>
HTML;

// Génération du PDF en paysage

Pdf::genererDocument($corps, 'Trombinoscope des étudiants', 'trombinoscope.pdf', false, false);
