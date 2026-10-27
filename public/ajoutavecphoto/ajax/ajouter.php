<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Config;
use ClasseTechnique\FileManager;
use ClasseTechnique\InputFileImg;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
Requete::exigerPost();

// récupération des données transmises
// Récupération des données textuelles de l'étudiant
$data = [
    'nom' => Requete::postString('nom'),
    'prenom' => Requete::postString('prenom'),
    'sexe' => Requete::postString('sexe'),
    'dateNaissance' => Requete::postString('dateNaissance'),
    'idOption' => Requete::postString('idOption'),
];

// Traitement du fichier (facultatif)
if (Requete::fichierEnvoye('fichier')) {
    $fichier = Requete::getFile('fichier');
    $lesParametres = Config::chargerPhp('etudiant');
    $inputFileImg = new InputFileImg($fichier, $lesParametres);

    if (!$inputFileImg->checkValidity()) {
        ReponseJson::envoyerErreur($inputFileImg->getValidationMessage());
    }

    $fileManager = new FileManager(DOSSIER_PHOTO_ETUDIANT, $lesParametres);

    // Sauvegarde physique de l'image (le nom retourné est celui réellement utilisé)
    $nomUnique = $fileManager->copier($fichier['tmp_name'], $inputFileImg->getValue());
    $data['photo'] = $nomUnique;
}

// Instanciation de l'objet métier
$etudiant = new Etudiant();

try {
    // Ajout de l'étudiant en BDD (les erreurs SQL/contraintes lèvent une exception)
    if (!$etudiant->add($data)) {
        ReponseJson::envoyerLesErreurs($etudiant->getErrors());
    }
} catch (\Throwable $e) {
    // Si la BDD échoue et qu'un logo avait été copié, on nettoie le disque
    if (isset($nomUnique)) {
        $fileManager->supprimer($nomUnique);
    }
    // Relance pour prise en charge par Erreur::installerGestionnaire()
    throw $e;
}

ReponseJson::envoyerMessage("L'étudiant a été ajouté avec succès.");

