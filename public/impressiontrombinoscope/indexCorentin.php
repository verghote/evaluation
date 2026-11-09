<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseMetier\Pdf;
use ClasseTechnique\InterfaceSystem;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

$lesEtudiants = Etudiant::getAll();
if ($lesEtudiants === []) {
    $interface = new InterfaceSystem(
        "Trombinoscope des étudiants",
        "Aucun étudiant n'a été trouvé.",
        "avertissement"
    );
    $interface->afficher();
}

$photoParDefaut = DOSSIER_PHOTO_ETUDIANT . '0.png';
$groupes = array_chunk($lesEtudiants, 32);
$corps = '';

/**
 * Génération du trombinoscope
 * Le script découpe la liste des étudiants en groupes de 32 étudiants,
 * puis pour chaque groupe, il génère un tableau HTML contenant 4 lignes de 8 colonnes.
 * Une balise propriétaire mPDF <pagebreak /> doit être explicitement injectée dans la chaîne de texte $corps
 * entre chaque groupe de 32 étudiants.
 * Conception rigide et peu flexible, mais qui permet un contrôle strict du rendu final.
 *
 */

foreach ($groupes as $numeroGroupe => $groupe) {
    if ($numeroGroupe > 0) {
        $corps .= '<pagebreak />';
    }

    $corps .= <<<HTML
<table class="center" style="width: 90%; table-layout: fixed; border-collapse: separate; border-spacing: 0.6mm; margin: 0 auto; page-break-inside: avoid;">
    <tbody>
HTML;

    for ($numeroLigne = 0; $numeroLigne < 4; $numeroLigne++) {
        $corps .= '<tr style="height: 37mm;">';

        for ($numeroColonne = 0; $numeroColonne < 8; $numeroColonne++) {
            $indexEtudiant = $numeroLigne * 8 + $numeroColonne;
            $etudiant = $groupe[$indexEtudiant] ?? null;

            if ($etudiant === null) {
                $corps .= '<td style="width: 12.5%; height: 37mm; padding: 0.4mm; border: none;"></td>';
                continue;
            }

            $nom = htmlspecialchars(
                mb_strtoupper($etudiant['nom'], 'UTF-8'),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
            $prenom = htmlspecialchars(
                mb_strtoupper($etudiant['prenom'], 'UTF-8'),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
            $photoEtudiant = $etudiant['present'] && isset($etudiant['photo']) ? DOSSIER_PHOTO_ETUDIANT . basename($etudiant['photo']) : $photoParDefaut;
            $photoSecurisee = htmlspecialchars($photoEtudiant, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $texteAlternatif = htmlspecialchars('Photo de ' . $etudiant['nomPrenom'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $corps .= <<<HTML
            <td class="center" style="width: 12.5%; height: 37mm; padding: 0.4mm; border: 0.25mm solid #ccc; vertical-align: middle;">
                <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin: 0; font-size: 7pt; line-height: 1.15;">
                    <tbody>
                        <tr><td class="center" style="border: none; padding: 1.5mm 0 1mm; vertical-align: middle;">$nom</td></tr>
                        <tr><td class="center" style="border: none; padding: 0.2mm; vertical-align: middle;">$prenom</td></tr>
                        <tr>
                            <td class="center" style="border: none; padding: 0.6mm 0 0; vertical-align: middle;">
                                <img src="$photoSecurisee" alt="$texteAlternatif" style="width: 23mm; height: 23mm; border: 0.25mm solid #ccc;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
HTML;
        }

        $corps .= '</tr>';
    }

    $corps .= <<<HTML
    </tbody>
</table>
HTML;
}

Pdf::genererDocument($corps, "Trombinoscope des étudiants", "trombinoscope_etudiants.pdf", false, true);
