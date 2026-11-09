"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {effacerLesErreurs} from "/composant/fonction/formulaire.js";
import {fichierValide, verifierImage} from "/composant/fonction/fichier.js";
import {appelAjax} from "/composant/fonction/ajax.js";
import {afficherToast, confirmer} from "/composant/fonction/afficher.js";
import {getData} from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// Récupération des données depuis le serveur
const lesEtudiants = getData("lesEtudiants");
const lesParametres = getData("lesParametres");
const repertoire = getData("repertoire");

// Extraction des paramètres
const lesExtensions = lesParametres.lesExtensions;
const maxSize = lesParametres.maxSize;
const redimensionner = lesParametres.resized;
const largeurMax = lesParametres.maxWidth;
const hauteurMax = lesParametres.maxHeight;

// Récupération des éléments de l'interface
const fichier = document.getElementById("fichier");
const lesCartes = document.getElementById("lesCartes");
const tplCarte = document.getElementById("tplCarteEtudiant");

// Identifiant de l'étudiant dont la photo doit être remplacée
let idEtudiant;

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// Lancer la fonction controlerFichier si un fichier a été sélectionné dans l'explorateur
fichier.addEventListener("change", () => {
    if (fichier.files.length > 0) {
        controlerFichier(fichier.files[0]);
    }

    // Permet de sélectionner à nouveau le même fichier.
    fichier.value = "";
});

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Crée une carte pour un étudiant.
 *
 * @param {Object} etudiant
 * @returns {HTMLElement}
 */
function creerCarte(etudiant) {
    const carte = tplCarte.content.firstElementChild.cloneNode(true);
    const cible = carte.querySelector(".cible-photo");
    const image = carte.querySelector(".carte-img");
    const zoneVide = carte.querySelector(".photo-vide");
    const boutonSupprimer = carte.querySelector(".btn-supprimer-photo");

    // Identification de la zone.
    cible.id = `cible${etudiant.id}`;

    // Nom et prénom de l'étudiant.
    carte.querySelector(".nom").textContent = etudiant.nom;
    carte.querySelector(".prenom").textContent = etudiant.prenom;

    // Photo.
    image.alt = `Photo de ${etudiant.nomPrenom}`;
    if (etudiant.present) {
        image.src = `${repertoire}/${encodeURIComponent(etudiant.photo)}?t=${Date.now()}`;
        image.hidden = false;
        zoneVide.hidden = true;
        boutonSupprimer.hidden = false;
    } else {
        image.removeAttribute("src");
        image.hidden = true;
        zoneVide.hidden = false;
        boutonSupprimer.hidden = true;
    }


    // Sélection d'une nouvelle photo.
    cible.addEventListener("click", () => {
        idEtudiant = etudiant.id;
        fichier.click();
    });


    // Glisser-déposer.
    cible.addEventListener("dragover", (event) => {
        event.preventDefault();
    });

    cible.addEventListener("drop", (event) => {
        event.preventDefault();
        idEtudiant = etudiant.id;
        const file = event.dataTransfer.files[0];
        if (file) {
            controlerFichier(file);
        }
    });

    // Suppression de la photo.
    boutonSupprimer.addEventListener("click", (event) => {
        // il faut stoopper la propagation de l'événement pour éviter que le clic sur le bouton ne déclenche également l'événement de clic sur la carte.
        event.stopPropagation();
        confirmer(() => supprimerPhoto(etudiant.id, image, zoneVide, boutonSupprimer))
    });
    return carte;
}

/**
 * Contrôle le fichier sélectionné au niveau de son extension et de sa taille
 * Contrôle les dimensions de l'image si le redimensionnement n'est pas demandé
 * lancer lad demande de remplacement de l'image
 * @param file {object} fichier à ajouter
 */
function controlerFichier(file) {
    // Efface les erreurs précédentes.
    effacerLesErreurs();

    // Vérification de la taille et de l'extension.
    if (!fichierValide(file, maxSize, lesExtensions)) {
        return;
    }

    // Vérifications spécifiques pour un fichier image
    // la fonction de rappel reçoit le fichier et l'image éventuellement redimensionnée
    verifierImage(file, redimensionner, largeurMax, hauteurMax, majPhoto);
}


/**
 * Remplace la photo de l'étudiant.
 *
 * @param {File} file
 */
function majPhoto(file) {

    // Création d'un objet FormData pour envoyer le fichier et l'identifiant de l'étudiant.
    const formData = new FormData();
    formData.append("fichier", file);
    formData.append("primaryKey", idEtudiant);

    // Appel AJAX pour mettre à jour la photo.
    appelAjax({
        url: "ajax/majphoto.php",
        data: formData,
        success: (data) => {
            const cible = document.getElementById(`cible${idEtudiant}`);
            const image = cible.querySelector(".carte-img");
            const zoneVide = cible.querySelector(".photo-vide");
            const boutonSupprimer = cible.closest(".carte-etudiant").querySelector(".btn-supprimer-photo");

            // Construction de l'URL de la nouvelle photo.
            image.src = `${repertoire}/${encodeURIComponent(data.photo)}?t=${Date.now()}`;

            // Force le navigateur à prendre en compte la nouvelle image.
            image.onload = () => {
                image.hidden = false;
                zoneVide.hidden = true;
                // Affiche le bouton de suppression.
                boutonSupprimer.hidden = false;
            };
            afficherToast("La photo a été mise à jour");
        }
    });
}


/**
 * Supprime la photo de l'étudiant.
 *
 * @param {number} id
 * @param {HTMLImageElement} image
 * @param {HTMLElement} zoneVide
 * @param {HTMLButtonElement} bouton
 */
function supprimerPhoto(id, image, zoneVide, bouton) {

    appelAjax({
        url: "ajax/supprimerphoto.php",
        data: {
            primaryKey: id
        },
        success: (data) => {
            // Suppression de la source de l'image.
            image.removeAttribute("src");
            // Masque l'image.
            image.hidden = true;
            // Affiche la zone permettant d'ajouter une photo.
            zoneVide.hidden = false;
            // Masque le bouton de suppression.
            bouton.hidden = true;
            afficherToast(data.message);
        }
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

const fragment = document.createDocumentFragment();

for (const etudiant of lesEtudiants) {
    fragment.appendChild(creerCarte(etudiant));
}

lesCartes.appendChild(fragment);
