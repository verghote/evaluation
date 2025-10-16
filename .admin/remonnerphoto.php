<?php
declare(strict_types=1);

// Configuration de l'accès à la base de données et au répertoire contenant les photos
const DB_DSN = 'mysql:host=localhost;dbname=etudiant;charset=utf8mb4';
const DB_USER = 'root';
const DB_PASS = '';

const PHOTO_DIR = __DIR__ . '/../data/photo/';

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


try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $sql = "SELECT id, photo FROM etudiant WHERE photo IS NOT NULL AND photo != ''";
    $stmt = $pdo->query($sql);
    $lesEtudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($lesEtudiants as $etudiant) {
        $id = $etudiant['id'];
        $photo = $etudiant['photo'];
        $ancienChemin = PHOTO_DIR . $photo;

        if (!is_file($ancienChemin)) {
            // Le fichier n’existe pas → remettre photo à NULL
            $pdo->prepare("UPDATE etudiant SET photo = NULL WHERE id = :id")
                ->execute(['id' => $id]);

            echo "Fichier introuvable pour l'étudiant ID $id → champ photo mis à NULL\n";
            continue;
        }

        // Nouveau nom nettoyé
        $nouveauNom = nettoyerNomFichier($photo);

        // Si rien ne change → inutile de renommer
        if ($nouveauNom === $photo) {
            echo "Aucun changement nécessaire pour $photo\n";
            continue;
        }

        $nouveauChemin = PHOTO_DIR . $nouveauNom;

        // Si ce nom nettoyé est déjà pris par un autre fichier ou répertoire
        if (file_exists($nouveauChemin)) {
            // Ajout d’un mini-hash pour éviter le conflit
            $nouveauNom = ajouterHash($nouveauNom);
            $nouveauChemin = PHOTO_DIR . $nouveauNom;
        }

        // Renommage du fichier dans le répertoire data
        $ok = rename($ancienChemin, $nouveauChemin);
        if ($ok) {
            // Mise à jour du nom dans la base de données
            $pdo->prepare("UPDATE etudiant SET photo = :photo WHERE id = :id")
                ->execute([
                    'photo' => $nouveauNom,
                    'id' => $id
                ]);
            echo "Renommé : $photo → $nouveauNom (ID $id)\n";
        } else {
            echo "Échec du renommage pour $photo\n";
        }
    }
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage() . "\n";
    exit(1);
}
