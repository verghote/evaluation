"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {ucWord} from "/composant/fonction/chaine.js";
import {activerTri} from "/composant/fonction/tableau.js";
import {getData} from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesEtudiants = getData('lesEtudiants');

const lesLignes = document.getElementById('lesLignes');
const modeleLigne = document.getElementById('modeleLigne');

// -----------------------------------------------------------------------------------
// Affichage des étudiants
// -----------------------------------------------------------------------------------

function afficher(lesEtudiants) {
    // Effacer les lignes précédentes
    lesLignes.replaceChildren();

    for (const etudiant of lesEtudiants) {

        // Cloner le modèle HTML
        const ligne = modeleLigne.content.firstElementChild.cloneNode(true);

        // Formater le nom
        const nomFormate = ucWord(etudiant.nomPrenom);

        // Détails affichés sur mobile
        const details =
            `👫 Sexe : ${etudiant.sexe}\n` +
            `🎂 Né(e) le : ${etudiant.dateNaissanceFr}`;

        // Récupérer les cellules
        const tdNom = ligne.querySelector('.col-nom');
        const tdSexe = ligne.querySelector('.col-sexe');
        const tdNaissance = ligne.querySelector('.col-naissance');
        const tdOption = ligne.querySelector('.col-option');
        const tdPhoto = ligne.querySelector('.col-photo');

        // Nom et prénom
        tdNom.textContent = nomFormate;
        tdNom.setAttribute('data-details', details);

        // Sexe
        tdSexe.textContent = etudiant.sexe;

        // Date de naissance
        tdNaissance.textContent = etudiant.dateNaissanceFr;

        // Option
        tdOption.textContent = etudiant.libelleCourt;

        // Photo
        const photo = etudiant.present
            ? etudiant.photo
            : '0.png';

        const img = tdPhoto.querySelector('img');
        img.src = '/data/photo/' + photo;
        img.alt = nomFormate;

        // Centrage des cellules concernées
        tdSexe.style.textAlign = 'center';
        tdNaissance.style.textAlign = 'center';
        tdOption.style.textAlign = 'center';
        tdPhoto.style.textAlign = 'center';

        // Ajouter la ligne au tableau
        lesLignes.appendChild(ligne);
    }
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

afficher(lesEtudiants);

// Activer le tri sur les colonnes
activerTri({
    idTable: "leTableau",
    getData: () => lesEtudiants,
    afficher: afficher,
    triInitial: {
        colonne: 'nomPrenom',
        ordre: "asc"
    }
});