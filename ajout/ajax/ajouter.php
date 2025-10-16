<?php

// création d'un objet etudiant pour réaliser les contrôles sur les données
$etudiant = new Etudiant();

// Les données ont-elles été transmises ?
if (!$etudiant->donneesTransmises()) {
    Erreur::envoyerReponse("Toutes les données attendues ne sont pas transmises", 'global');
}

// Toutes les données sont-elles valides ?
if (!$etudiant->checkAll()) {
    Erreur::envoyerReponse("Certaines données transmises ne sont pas valides", 'global');
}

// Si un fichier est téléversé, il faut le controler
if (isset($_FILES['fichier'])) {
    // instanciation et paramétrage d'un objet InputFile
    $file = new InputFile($_FILES['fichier'], etudiant::getConfig());
    // vérifie la validité du fichier
    if (!$file->checkValidity()) {
        Erreur::envoyerReponse($file->getValidationMessage(), 'global');
    }

    // La photo sera enregistrée  sous le nom : nom_prenom.extension en minuscules
    $extension = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);
// le prénom peut contenir des accents qu'il faut remplacer par des caractères non accentués
    $prenom = Std::supprimerAccent($_POST['prenom']);
    $file->Value = strtolower( $_POST['nom'] . '_' . $prenom . '.' . $extension);

}


// Alimentation éventuelle de la colonne 'photo'  : sa valeur  est stockée dans la propriété  Value de  l'objet $file
if (isset($file)) {
    $etudiant->setValue('photo', $file->Value);
}
// Ajout dans la table etudiant
$etudiant->insert();

// Récupération de l'identifiant de l'étudiant ajouté
$id =  $etudiant->getLastInsertId();

// copie  éventuelle du fichier dans le répertoire de stockage
if (isset($file)) {
    $ok = $file->copy();
    // en cas d'échec (peu probable) il faut supprimer l'enregistrement créé afin de conserver une cohérence
    if (!$ok) {
        $etudiant->delete($id);
        Erreur::envoyerReponse("L'ajout a échoué car le logo n'a pas pu être téléversé", 'global');
    }
}
// Tout est OK
$reponse = ['success' => $id];
echo json_encode($reponse, JSON_UNESCAPED_UNICODE);
