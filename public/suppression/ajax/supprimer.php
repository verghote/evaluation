<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\FileManager;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
Requete::exigerPost();

// récupération des données transmises
$id = Requete::postInt('primaryKey');

// Recherche de l'étudiant dans la base pour obtenir le nom de la photo associée
$unEtudiant = Etudiant::getById($id);
if ($unEtudiant === null) {
    ReponseJson::envoyerErreur("L'étudiant demandé n'existe pas.");
}

$photo = $unEtudiant['photo'];

// Création de l'objet métier
$etudiant = new Etudiant();

// Suppression de l'étudiant dans la base de données
$etudiant->delete($id);

// Si aucune photo n'est associée, on valide immédiatement la suppression
if ($photo === null) {
    ReponseJson::envoyerMessage("L'étudiant a été supprimé.");
}

// Suppression de la photo associée à l'étudiant si elle existe

// création d'un objet FileManager pour gérer les fichiers photo
$fileManager = new FileManager(DOSSIER_PHOTO_ETUDIANT);

// Vérification de l'existence du fichier avant de tenter de le supprimer
if (!$fileManager->existe($photo)) {
    ReponseJson::envoyerMessage("L'étudiant a été supprimé, mais la photo associée n'a pas été trouvée sur le disque.");
}

// Tentative de suppression du fichier photo
if (!$fileManager->supprimer($photo)) {
    ReponseJson::envoyerMessage("L'étudiant a été supprimé de la base de données, mais la photo n'a pas pu être retirée du disque.");
}

// Tout s'est bien passé, l'étudiant et sa photo ont été supprimés avec succès
ReponseJson::envoyerMessage("L'étudiant et sa photo ont été supprimés.");


