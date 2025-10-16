"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from '/composant/fonction/ajax.js';
import { creerSelect} from '/composant/fonction/formulaire.js';
import {getTd, getTr} from "/composant/fonction/trtd.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesEtudiants, lesOptions */

const lesLignes = document.getElementById('lesLignes');
const msg = document.getElementById('msg');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Crée et retourne une ligne de tableau représentant une etudiant.
 * @param {object} etudiant - Objet etudiant avec id, dateFr, nom, actif
 * @returns {HTMLTableRowElement}
 */
function creerLigneetudiant(etudiant) {

    // 1. Colonne nom prénom
    const tdNomPrenom = getTd(etudiant.nomPrenom);

    // 2. Colonne option sous la forme d'une zone de liste déroulante
    const idOption = creerSelect();
    // alimentation de la liste des options
    for (const element of lesOptions) {
        idOption.add(new Option(element.libelleLong, element.id));
    }
    // sélection de l'option de l'étudiant
    idOption.value = etudiant.idOption;

    idOption.onchange = () => {
        appelAjax({
            url: '/ajax/modifiercolonne.php',
            data: {
                table: 'etudiant',
                colonne: 'idOption',
                valeur: idOption.value,
                id: etudiant.id,
            },
            success : () => idOption.style.border = '2px solid green'
        });
    };

    const tdIdOption = getTd('');
    tdIdOption.appendChild(idOption);

    // Création de la ligne
    const tr = getTr([tdNomPrenom, tdIdOption]);
    tr.id = etudiant.id;

    return tr;
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

lesLignes.innerHTML = '';
for (const etudiant of lesEtudiants) {
    const ligne = creerLigneetudiant(etudiant);
    lesLignes.appendChild(ligne);
}
