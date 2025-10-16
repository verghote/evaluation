"use strict";
// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {retournerVersApresConfirmation} from "/composant/fonction/afficher.js";
import {
    configurerFormulaire,
    configurerDate,
    donneesValides,
    filtrerLaSaisie,
    enleverAccent,
    supprimerEspace,
    fichierValide,
    verifierImage,
    effacerLesErreurs
} from "/composant/fonction/formulaire.js";
import {ucFirst} from '/composant/fonction/format.js';

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesOptions, dateMin, dateMax , lesParametres*/

const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');
const idOption = document.getElementById('idOption');
const msg = document.getElementById('msg');
const btnAjouter = document.getElementById('btnAjouter');

const fichier = document.getElementById('fichier');
const nomFichier = document.getElementById('nomFichier');
const cible = document.getElementById('cible');
const label = document.getElementById('label');

// fichier téléversé
let leFichier = null;

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

btnAjouter.onclick = () => {
    // mise en forme des données
    nom.value = enleverAccent(supprimerEspace(nom.value)).toUpperCase();
    prenom.value = ucFirst(supprimerEspace(prenom.value).toUpperCase());
    // contrôle des champs de saisie
    msg.innerHTML = "";
    if (donneesValides()) {
        ajouter();
    }
};

// Déclencher le clic sur le champ de type file lors d'un clic dans la zone cible
cible.onclick = () => fichier.click();

// // ajout du glisser déposer dans la zone cible
cible.ondragover = (e) => e.preventDefault();
cible.ondrop = (e) => {
    e.preventDefault();
    controlerFichier(e.dataTransfer.files[0]);
};

// Lancer la fonction controlerFichier si un fichier a été sélectionné dans l'explorateur
fichier.onchange = () => {
    if (fichier.files.length > 0) {
        controlerFichier(fichier.files[0]);
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function ajouter() {
    // Alimentation de l'objet formData pour le transfert des données
    const formData = new FormData();
    formData.append('table', 'etudiant');
    formData.append('nom', nom.value);
    formData.append('prenom', prenom.value);
    formData.append('sexe', sexe.value);
    formData.append('dateNaissance', dateNaissance.value);
    formData.append('idOption', idOption.value);

    // la photo n'est pas obligatoire
    if (leFichier !== null) {
        formData.append('fichier', leFichier);
    }


    //  demande d'ajout dans la base de données
    appelAjax({
        url: 'ajax/ajouter.php',
        data: formData,
        success: () => retournerVersApresConfirmation("Etudiant ajouté", '/liste')
    });
}


/**
 * Contrôle le fichier sélectionné au niveau de son extension et de sa taille
 * Contrôle les dimensions de l'image si le redimensionnement n'est pas demandé
 * Affiche le nom du fichier ou un message d'erreur
 * @param file {object} fichier à ajouter
 */

function controlerFichier(file) {
    // Efface les erreurs précédentes
    effacerLesErreurs();
    // Vérification de taille et d'extension
    if (!fichierValide(file, lesParametres)) {
        return;
    }
    // Vérifications spécifiques pour un fichier image
    // La fonction de rappel reçoit implicitement en paramètre l'objet file et l'objet Image créé
    verifierImage(file, lesParametres,
        (file, img) => {
            nomFichier.innerText = file.name;
            leFichier = file;
            cible.innerHTML = "";
            cible.appendChild(img);
        }
    );
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// alimentation de la zone de liste des options
for (const element of lesOptions) {
    idOption.add(new Option(element.libelleLong, element.id));
}

// Selection de l'option par défaut' TC
idOption.selectedIndex = 2;

// contrôle des données
configurerFormulaire();
filtrerLaSaisie('nom', /[A-Za-z '-]/);
filtrerLaSaisie('prenom', /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/);


// La date doit être comprise entre dateMin et dateMax
configurerDate(dateNaissance, {
    min: dateMin,
    max: dateMax,
    valeur: dateMax
});

fichier.accept = lesParametres.accept;
label.innerText = lesParametres.label;

// Données de test
nom.value = 'Dupont';
prenom.value = 'Hervé';
dateNaissance.value = '2005-10-01';
