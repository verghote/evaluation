"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {
    configurerFormulaire,
    effacerLesErreurs,
    fichierValide,
    verifierImage
} from "/composant/fonction/formulaire.js";
import {appelAjax} from "/composant/fonction/ajax.js";
import {afficherToast} from "/composant/fonction/afficher";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesParametres, lesEtiudiants*/

const fichier = document.getElementById('fichier');
const nomFichier = document.getElementById('nomFichier');
const label = document.getElementById('label');
const msg = document.getElementById('msg');

// identifiant de l'étudiant en cours de modification
let idEtudiant;

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// traitement du champ file associé aux modifications de photos
fichier.onchange = function () {
    if (this.files.length > 0) {
        controlerFichier(this.files[0]);
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Crée une carte pour afficher les informations d'un club
 * @param {Object} element - Données du club
 * @returns {HTMLDivElement}
 */
function creerCarte(element) {
    // 📦 Création de la carte principale
    const carte = document.createElement('div');
    carte.classList.add('card', 'carte-club', 'shadow-sm');

    // Entête de la carte
    const entete = document.createElement('div');
    entete.classList.add('card-header', 'text-center');
    entete.style.height = '80px';

    // Couleurs dynamiques depuis les variables CSS
    entete.style.backgroundColor = getComputedStyle(document.documentElement).getPropertyValue('--background-color-header').trim();
    entete.style.color = getComputedStyle(document.documentElement).getPropertyValue('--text-color-header').trim();
    entete.innerText = element.nomPrenom;

    // Corps
    const corps = document.createElement('div');
    corps.classList.add('card-body');
    corps.style.height = '175px';

    // Génération de la balise contenant la photo
    const cible = document.createElement('div');
    cible.id = 'cible' + element.id;

    const img = document.createElement('img');
    img.alt = "Photo de l'étudiant";
    img.style.maxHeight = '100%';
    img.style.maxWidth = '100%';
    img.style.objectFit = 'contain';
    img.style.display = 'block';
    img.style.margin = '0 auto'; // centrer horizontalement
    // Déclencher le clic sur le champ de type file lors d'un clic sur la photo
    img.onclick = () => {
        idEtudiant = element.id;
        fichier.click();
    }
    // // ajout du glisser déposer sur la balise img
    img.ondragover = (e) => e.preventDefault();
    img.ondrop = (e) => {
        idEtudiant = element.id;
        e.preventDefault();
        controlerFichier(e.dataTransfer.files[0]);
    };
    if (element.present) {
        img.src = lesParametres.repertoire + '/' + element.photo + '?t=' + new Date().getTime(); // Pour éviter la mise en cache
    } else {
        img.src = lesParametres.repertoire + '/0.png';
    }
    cible.appendChild(img);
    corps.appendChild(cible);

    carte.appendChild(entete);
    carte.appendChild(corps);

    carte.style.flexGrow = '0';          // La carte ne grandit pas au-delà de sa base
    carte.style.flexShrink = '1';        // Elle peut se rétrécir si l'espace est limité
    carte.style.flexBasis = '240px';     // Largeur de base (tailleimg)
    carte.style.maxWidth = '100%';       // Elle ne dépasse jamais la largeur de son conteneur

    return carte;
}

/**
 * Contrôle le fichier sélectionné au niveau de son extension et de sa taille
 * Contrôle les dimensions de l'image si le redimensionnement n'est pas demandé
 * lancer lad demande de remplacement de l'image
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
    verifierImage(file, lesParametres, majPhoto);
}

/**
 * Remplace le fichier sélectionné par le nouveau fichier téléversé
 * @param file
 * @param img
 */
function majPhoto(file, img) {
    // transfert du fichier vers le serveur dans le répertoire sélectionné
    const formData = new FormData();
    formData.append('fichier', file);
    formData.append('id', idEtudiant);
    appelAjax({
        url: 'ajax/majphoto.php',
        data: formData,
        success: (data) => {
            afficherToast("La photo a été mise à jour");
            // afficher la nouvelle photo
            const cible = document.getElementById('cible' + idEtudiant);
            cible.innerHTML = "";
            cible.appendChild(img);
            // afficher le bouton du suppression de la photo
            const carte = cible.closest('.carte-club');
            const btnSupprimer = carte.querySelector('button');
            if (btnSupprimer) {
                btnSupprimer.style.display = 'inline-block';
            }
        }
    });
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

fichier.accept = lesParametres.accept;

// Injection dynamique d'une règle CSS responsive
const style = document.createElement('style');
style.textContent = `
    @media screen and (max-width: 600px) {
        .carte-club {
            flex-basis: 100% !important;
            background-color: lightyellow !important;
        }
    }
`;
document.head.appendChild(style); // On ajoute la balise <style> dans le <head>

//  Mise en forme du conteneur flex
lesCartes.style.display = 'flex';              // Flexbox activé
lesCartes.style.flexWrap = 'wrap';             // Retour à la ligne si besoin
lesCartes.style.gap = '1rem';                  // Espacement entre les cartes
lesCartes.style.justifyContent = 'flex-start'; // Alignement des cartes à gauche

// Génération des cartes à partir des données
for (const element of lesEtudiants) {
    const carte = creerCarte(element);
    lesCartes.appendChild(carte);
}




