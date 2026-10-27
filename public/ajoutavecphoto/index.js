"use strict";
// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {genererMessage, retournerVers} from "/composant/fonction/afficher.js";
import {configurerFormulaire, configurerDate, donneesValides, filtrerLaSaisie, effacerLesErreurs } from "/composant/fonction/formulaire.js";
import {enleverAccent, supprimerEspace, ucFirst} from '/composant/fonction/chaine.js';
import {getData} from "/composant/fonction/page.js";
import {getDateRelative} from '/composant/fonction/date.js';
import {fichierValide, verifierImage} from "/composant/fonction/fichier.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesOptions = getData("lesOptions");
const lesEtudiants = getData("lesEtudiants");
const lesParametres = getData('lesParametres');
const lesExtensions = lesParametres.lesExtensions;
const maxSize = lesParametres.maxSize;
const redimensionner = lesParametres.resized;
const width = lesParametres.maxWidth;
const height = lesParametres.maxHeight;

const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');
const idOption = document.getElementById('idOption');
const msg = document.getElementById('msg');
const btnAjouter = document.getElementById('btnAjouter');

// Fichier téléversé
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
    if (lesEtudiants.some(c => c.nom === nom.value && c.prenom === prenom.value)) {
        msg.innerHTML = genererMessage("Un étudiant avec ce nom et ce prénom est déjà enregistré.");
        return;
    }

    if (donneesValides()) {
        ajouter();
    }
};


// Déclencher le clic sur le champ de type file lors d'un clic dans la zone cible
cible.onclick = () => fichier.click();

// Ajout du glisser-déposer dans la zone cible
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

    effacerLesErreurs();
    const formData = new FormData();
    formData.append('nom', nom.value);
    formData.append('prenom', prenom.value);
    formData.append('sexe', sexe.value);
    formData.append('dateNaissance', dateNaissance.value);
    formData.append('idOption', idOption.value);

    // La photo n'est pas obligatoire
    if (leFichier !== null) {
        formData.append('fichier', leFichier);
    }

    appelAjax({
        url: 'ajax/ajouter.php',
        data: formData,
        success: (reponse) => retournerVers(reponse.message, "/liste")
    });
}

/**
 * Contrôle le fichier sélectionné au niveau de son extension et de sa taille
 * Contrôle les dimensions de l'image si le redimensionnement n'est pas demandé
 * Affiche le nom du fichier ou un message d'erreur
 * @param file {object} fichier à ajouter
 */
function controlerFichier(file) {
    effacerLesErreurs();

    // Vérification de taille et d'extension
    if (!fichierValide(file, maxSize, lesExtensions)) {
        return;
    }

    // Vérifications spécifiques pour un fichier image
    verifierImage(file, redimensionner, width, height,
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

// alimentation de la zone de liste des clubs
for (const element of lesOptions) {
    idOption.add(new Option(element.libelleLong, element.id));
}

// Selection de l'option par défaut' TC
idOption.selectedIndex = 2;

// contrôle des données
configurerFormulaire();
filtrerLaSaisie('nom', /[A-Za-z '-]/);
filtrerLaSaisie('prenom', /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/);


// définition des bornes de la date de naissance (entre 17 et 25 ans)
configurerDate(dateNaissance, {
    min:getDateRelative('annee', -25),
    max: getDateRelative('annee', -17),
    valeur: getDateRelative('annee', -19),
});

// Données de test
nom.value = 'Dûpont';
prenom.value = 'Hervé';

