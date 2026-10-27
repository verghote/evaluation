<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Config;
use ClasseTechnique\ImageManager;
use ClasseTechnique\InputFileImg;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

// Chargement automatique des classes
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX POST sécurisé
Requete::exigerPost();

// Récupération des données de la requête
$id = Requete::postInt('primaryKey');
$file = Requete::getFile('fichier');

// Vérification de la présence du fichier
if (!$file) {
    ReponseJson::envoyerErreur("Aucun fichier n'a été transmis.");
}

// Recherche de l'étudiant pour vérifier son existence et récupérer le photo actuelle
$unEtudiant = Etudiant::getById($id);
if ($unEtudiant === []) {
    ReponseJson::envoyerErreur("Ce étudiant n'existe pas.");
}

$anciennePhoto = $unEtudiant['photo'];

// Instanciation d'un nouvel objet InputFileImg
$lesParametres = Config::chargerPhp('etudiant');
$inputFileImg = new InputFileImg($file, $lesParametres);

// Vérification de la validité du fichier image
if (!$inputFileImg->checkValidity()) {
    ReponseJson::envoyerErreur($inputFileImg->getValidationMessage());
}

// Instanciation d'un objet FileManager
$imageManager = new ImageManager(DOSSIER_PHOTO_ETUDIANT, $lesParametres);

// Sauvegarde physique de la nouvelle photo (nom d'origine, renommé si nécessaire)
$nouvellePhoto = $imageManager->copierImage($inputFileImg, $inputFileImg->getValue());

// Instanciation de l'objet métier
$etudiant = new Etudiant();

// Mise à jour de la base de données avec le nom de la nouvelle photo
// Si la mise à jour de la BDD échoue, suppression de la photo créée sur le disque
try {
    // modify($id, $data) retourne false en cas d'erreur de validation métier
    if (!$etudiant->modify($id, ['photo' => $nouvellePhoto])) {
        $imageManager->supprimer($nouvellePhoto);
        ReponseJson::envoyerLesErreurs($etudiant->getErrors());
    }
} catch (\Throwable $e) {
    $imageManager->supprimer($nouvellePhoto);
    throw $e;
}

// Si la BDD est mise à jour avec succès et qu'il existe une ancienne photo, suppression de l'ancienne photo du disque
if ($anciennePhoto !== null) {
    $imageManager->supprimer($anciennePhoto);
}

// Envoi de la réponse JSON : envoi du nom de la nouvelle photo pour mise à jour côté client
ReponseJson::envoyerLesDonnees(['photo' => $nouvellePhoto]);



