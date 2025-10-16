<?php
declare(strict_types=1);

// Fuseau horaire correct
date_default_timezone_set('Europe/Paris');

// Constantes de configuration
const DB_DSN = 'mysql:host=localhost;dbname=etudiant;charset=utf8mb4';
const DB_USER = 'root';
const DB_PASS = '';

const PHOTO_DIR = __DIR__ . '/../data/photo/';
const LOG_FILE = __DIR__ . '/controlephoto.log';

// Fonction utilitaire d’écriture dans le fichier log
function ecrireLog(string $message): void
{
    file_put_contents(LOG_FILE, $message . PHP_EOL, FILE_APPEND);
}

// Horodatage en début de log
$horodatage = date('Y-m-d H:i:s');
ecrireLog('');
ecrireLog(str_repeat('-', 20) . " Vérification photos - $horodatage");

// Initialisation
$photosDeclarees = [];
$photosPhysiques = [];

// Connexion à la base
try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 1. Récupération des photos déclarées dans la BDD
    $sql = "SELECT id, photo FROM etudiant WHERE photo IS NOT NULL AND photo != ''";
    $stmt = $pdo->query($sql);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultats as $ligne) {
        $photo = $ligne['photo'];
        $id = $ligne['id'];
        $photosDeclarees[$photo] = $id;
    }

    // 2. Lecture des fichiers du dossier photo
    $fichiers = scandir(PHOTO_DIR);
    foreach ($fichiers as $fichier) {
        if ($fichier === '.' || $fichier === '..') {
            continue;
        }
        if (!is_file(PHOTO_DIR . $fichier)) continue;

        $photosPhysiques[] = $fichier;
    }

    // 3. Vérification : chaque photo déclarée existe-t-elle ?
    $erreurs = 0;

    foreach ($photosDeclarees as $photo => $id) {
        if (!in_array($photo, $photosPhysiques)) {
            ecrireLog("Manquante : $photo (déclarée pour ID $id)");
            $erreurs++;
        }
    }

    // 4. Vérification : chaque fichier physique est-il déclaré ?
    foreach ($photosPhysiques as $photo) {
        if ($photo === '0.png') continue; // Photo par défaut
        if (!array_key_exists($photo, $photosDeclarees)) {
            ecrireLog("Orpheline : $photo (non associée à un étudiant)");
            $erreurs++;
        }
    }

    if ($erreurs === 0) {
        ecrireLog("Tout est cohérent");
    } else {
        ecrireLog("$erreurs anomalie(s) détectée(s).");
    }

    echo "Contrôle terminé. Voir le log : " . LOG_FILE . "\n";

} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage() . "\n";
    exit(1);
}

