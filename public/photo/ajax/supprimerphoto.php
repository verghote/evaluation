<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\FileManager;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

// Chargement automatique des classes
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX POST sécurisé
Requete::exigerPost();

// Récupération des données de la requête
$id = Requete::postInt('primaryKey');

// Recherche de l'étudiant pour vérifier son existence et récupérer la photo actuelle
$unEtudiant = Etudiant::getById($id);
if ($unEtudiant === null) {
    ReponseJson::envoyerErreur("Ce étudiant n'existe pas.");
}

$anciennePhoto = $unEtudiant['photo'];

// si le photo est null, on ne fait rien
if ($anciennePhoto === null) {
    ReponseJson::envoyerErreur("L'étudiant n'a pas de photo à supprimer.");
}

// Création de l'objet métier
$etudiant = new Etudiant();

// Mise à jour de la colonne photo qui prend la valeur null
if (!$etudiant->modify($id, ['photo' => null])) {
    ReponseJson::envoyerLesErreurs($etudiant->getErrors());
}

// Instanciation du FileManager pour supprimer le fichier du disque
$fileManager = new FileManager(DOSSIER_PHOTO_ETUDIANT);

// Vérification de l'existence du fichier avant de tenter de le supprimer
if (!$fileManager->existe($anciennePhoto)) {
    ReponseJson::envoyerMessage("La photo de l'étudiant a été supprimée de la base de données, mais le fichier n'existait pas sur le disque.");
}

// Tentative de suppression du fichier photo
if (!$fileManager->supprimer($anciennePhoto)) {
    ReponseJson::envoyerMessage("La photo de l'étudiant a été supprimée de la base de données, mais le fichier n'a pas pu être retiré du disque.");
}
// Tout s'est bien passé, l'étudiant et sa photo ont été supprimés avec succès
ReponseJson::envoyerMessage("La photo de l'étudiant a été supprimée de la base de données et du disque.");
