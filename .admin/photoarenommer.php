<?php
declare(strict_types=1);

// Eviter les problèmes de fuseau horaire au niveau de l'horadatage
date_default_timezone_set('Europe/Paris');

// Configuration de l'accès à la base de données et au répertoire contenant les photos
const DB_DSN = 'mysql:host=localhost;dbname=etudiant;charset=utf8mb4';
const DB_USER = 'root';
const DB_PASS = '';

const PHOTO_DIR = __DIR__ . '/../data/photo/';

const LOG_FILE = __DIR__ . '/photoarenommer.log';
const HASH_LENGTH = 5;
// -----------------------------------------------------------

/**
 * Nettoie un nom de fichier : accents, espaces, caractères spéciaux
 */
function nettoyerNomFichier(string $nom): string
{
    // Remplacement des caractères accentués (translittération)
    $nom = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nom);

    // Supprime les caractères indésirables sauf : lettres, chiffres, tirets, points, underscores, espaces
    $nom = preg_replace('/[^A-Za-z0-9.\- _]/', '', $nom);

    // Remplace plusieurs espaces (ou tabs) par un seul espace
    $nom = preg_replace('/\s+/', ' ', $nom);

    // Trim les espaces au début et fin
    $nom = trim($nom);

    // Espaces → underscores
    $nom = str_replace(' ', '_', $nom);

    // Tout en minuscules
    return strtolower($nom);
}

/**
 * Ajoute un mini-hash à un nom de fichier (avant l’extension)
 */
function ajouterHash(string $nomFichier): string
{
    $ext = pathinfo($nomFichier, PATHINFO_EXTENSION);
    $base = pathinfo($nomFichier, PATHINFO_FILENAME);
    $hash = substr(sha1($nomFichier), 0, HASH_LENGTH);
    return "{$base}_{$hash}.{$ext}";
}

function ecrireLog(string $message): void
{
    file_put_contents(LOG_FILE, $message . PHP_EOL, FILE_APPEND);
}

// LANCEMENT -----------------------------------------------------

// Horodatage du début d'exécution
$horodatage = date('Y-m-d H:i:s');
ecrireLog('');
ecrireLog(str_repeat('-', 20) . " Exécution du : $horodatage");

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $sql = "SELECT id, photo FROM etudiant WHERE photo IS NOT NULL AND photo != ''";
    $cmd = $pdo->query($sql);
    $lesEtudiants = $cmd->fetchAll(PDO::FETCH_ASSOC);

    $nbFichierRenomme = 0;
    $nbFichierInexistant = 0;

    foreach ($lesEtudiants as $etudiant) {
        $photo = $etudiant['photo'];
        $ancienChemin = PHOTO_DIR . $photo;

        if (!is_file($ancienChemin)) {
            ecrireLog("$photo => ce fichier n'existe pas");
            $nbFichierInexistant++;
            continue;
        }

        $nouveauNom = nettoyerNomFichier($photo);
        if ($photo === $nouveauNom) {
            continue; // pas de changement
        }

        $nbFichierRenomme++;
        $nouveauChemin = PHOTO_DIR . $nouveauNom;
        // Si ce nom nettoyé est déjà pris par un autre fichier ou répertoire
        if (file_exists($nouveauChemin)) {
            $nomAvecHash = ajouterHash($nouveauNom);
            ecrireLog("$photo => $nomAvecHash");
        } else {
            ecrireLog("$photo => $nouveauNom");
        }
    }

    if ($nbFichierRenomme === 0) {
        ecrireLog("Aucun fichier concerné par le renommage.");
    } else {
        ecrireLog("$nbFichierRenomme fichier(s) à renommer.");
    }
    if ($nbFichierInexistant > 0) {
        ecrireLog("$nbFichierInexistant fichier(s) introuvable(s).");
    }
    echo "Rapport généré dans " . LOG_FILE . "\n";

} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage() . "\n";
    exit(1);
}

