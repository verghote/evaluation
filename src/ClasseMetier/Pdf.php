<?php
declare(strict_types=1);

namespace ClasseMetier;

use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class Pdf
{

    /**
     * Génère le document PDF selon l'orientation demandée.
     */
   public static function genererDocument(string $corps, string $titre, string $nomFichier, bool $telecharger = false, bool $paysage = false): void
    {
        $mpdf = new Mpdf([
            'format' => $paysage ? 'A4-L' : 'A4',
            'margin_top' => 33,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);

        // Pour l'en-tête répété des tableaux, mPDF supporte le chemin absolu système.
        $cheminLogo = $_SERVER['DOCUMENT_ROOT'] . '/data/logo.png';

        // 1. Ajout des styles CSS spécifiques aux tableaux.
        $styles = <<<CSS
<style>
    body {
        font-family: helvetica, sans-serif;
        font-size: 11pt;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10pt;
        margin-top: 10px;
    }

    th, td {
        border: 1px solid #000;
        padding: 6px 4px;
        text-align: left;
    }

    th {
        background-color: #dde;
    }

    td {
        vertical-align: top;
    }

    .center {
        text-align: center;
    }
</style>
CSS;

        $mpdf->WriteHTML($styles, HTMLParserMode::HEADER_CSS);

        // 2. Entête : logo + titre (affiché sur chaque page).
        $titreSecurise = htmlspecialchars($titre, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $logoSecurise = htmlspecialchars($cheminLogo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $entete = <<<HTML
<table style="width: 100%; border-collapse: collapse; border-bottom: 1px solid #aaa;">
    <tr>
        <td style="width: 20%; vertical-align: middle; border: none;">
            <img src="LOGO_PDF" style="max-height: 70px;" alt="Logo">
        </td>
        <td style="text-align: left; font-size: 14pt; vertical-align: middle; border: none;">
            <strong>TITRE_PDF</strong>
        </td>
    </tr>
</table>
HTML;

        $entete = str_replace(['LOGO_PDF', 'TITRE_PDF'], [$logoSecurise, $titreSecurise], $entete);

        $mpdf->SetHTMLHeader($entete);

        // 3. Pied : pagination automatique.
        $dateEdition = (new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN,
            'EEEE d MMMM yyyy'
        ))->format(new \DateTime('now'));
        $pied = <<<HTML
            <div style="font-size: 10pt; border-top: 1px solid #aaa; padding-top: 5px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 70%; text-align: left; border: none; padding: 0;">
                            Date édition : {$dateEdition}
                        </td>
                        <td style="width: 30%; text-align: right; border: none; padding: 0;">
                            Page {PAGENO} / {nb}
                        </td>
                    </tr>
                </table>
            </div>
HTML;

        $mpdf->SetHTMLFooter($pied);

        // 4. Contenu principal (tableau ou liste).
        $mpdf->WriteHTML($corps);

        // 5. Génération du fichier avec nom sécurisé.
        $nomFichierSecurise = basename($nomFichier);
        $mpdf->Output($nomFichierSecurise, $telecharger ? Destination::DOWNLOAD : Destination::INLINE);
    }
}
