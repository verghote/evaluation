"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from '/composant/fonction/ajax.js';
import {afficherToast, confirmer} from '/composant/fonction/afficher.js';
import { creerBoutonSuppression} from '/composant/fonction/formulaire.js';
import {getTd, getTr} from "/composant/fonction/trtd.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesEtudiants */

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

    // 1. Colonne des actions (supprimer)
    const supprimer = () =>
        appelAjax({
            url: 'ajax/supprimer.php',
            data: { id: etudiant.id },
            success: () => {
                tr.remove();
                afficherToast("Etudiant supprimé");
            }
        });

    const actionSupprimer = () => confirmer(supprimer);

    const container = document.createElement('div');
    Object.assign(container.style, {
        display: 'flex',
        gap: '8px',
        alignItems: 'center'
    });

    container.appendChild(creerBoutonSuppression(actionSupprimer));

    const tdAction = getTd('');
    tdAction.appendChild(container);

    // 2. Colonne nom prénom
    const tdNomPrenom = getTd(etudiant.nomPrenom);

    // Création de la ligne
    const tr = getTr([tdAction, tdNomPrenom]);
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
