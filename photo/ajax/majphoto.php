<?php
// contrôle de la présence du fichier transmis
if (!isset($_FILES['fichier']) ) {
    Erreur::envoyerReponse("La photo n'est pas transmise", 'global');
}

// instanciation et paramétrage d'un objet InputFileImg
$file = new InputFileImg($_FILES['fichier'], Etudiant::getConfig());

// vérifie la validité du fichier
if (!$file->checkValidity()) {
    Erreur::envoyerReponse($file->getValidationMessage(), 'global');
}

// vérification du paramètre id
if (!isset($_POST['id']) || empty($_POST['id'])) {
    Erreur::afficherReponse("L'identifiant de l'étudiant n'est pas transmis", 'global');
}

// récupération du paramètre attendu
$id = (int)$_POST['id'];

// contrôle de la validité du paramètre
if (!preg_match('/^[0-9]+$/', $id)) {
    Erreur::bloquerVisiteur();
}

// vérification de l'existence de l'étudiant
$etudiant = Etudiant::getById($id);
if (!$etudiant) {
    Erreur::envoyerReponse("Cet étudiant n'existe pas", 'global');
}

// La photo sera enregistrée  sous le nom : nom_prenom.extension en minuscules
$extension = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);
// le prénom peut contenir des accents qu'il faut remplacer par des caractères non accentués
$prenom = Std::supprimerAccent($etudiant['prenom']);
$file->Value = strtolower( $etudiant['nom'] . '_' . $prenom . '.' . $extension);

// copier la nouvelle photo en mode update afin d'accepter le remplacement
$file->Mode = 'update';
$ok = $file->copy();
if (!$ok) {
    Erreur::envoyerReponse("Le téléversement de laphoto a échoué", 'global');
}

// supprimer l'ancienne photo si elle existe et si son nom est différent de celui de la nouvelle photo
if (!empty($etudiant['photo']) && $etudiant['photo'] !== $file->Value) {
    Etudiant::supprimerPhoto($etudiant['photo']);
}

// mettre à jour le champ logo de l'enregistrement
Etudiant::majPhoto($id, $file->Value);

// tout est ok
echo json_encode(['success' => "La photo a été remplacée"], JSON_UNESCAPED_UNICODE );

