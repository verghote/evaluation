
"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from '/composant/fonction/ajax.js';
import {afficherToast} from '/composant/fonction/afficher.js';
import {getData} from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesEtudiants = getData("lesEtudiants");
const lesOptions = getData("lesOptions");

const lesLignes = document.getElementById('lesLignes');
const modeleLigne = document.getElementById('modeleLigne');

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Crée une ligne de tableau représentant un étudiant.
 * @param {Object} etudiant
 * @returns {HTMLTableRowElement}
 */
function creerLigneEtudiant(etudiant) {

    // Cloner le modèle HTML
    const tr = modeleLigne.content.firstElementChild.cloneNode(true);

    // Récupérer les éléments
    const tdNomPrenom = tr.querySelector('.col-nom-prenom');
    const selectOption = tr.querySelector('.select-option');

    // Colonne nom et prénom
    tdNomPrenom.textContent = etudiant.nomPrenom;

    // Alimenter la liste des options
    for (const option of lesOptions) {
        selectOption.add(
            new Option(option.libelleLong, option.id)
        );
    }

    // Sélectionner l'option actuelle de l'étudiant
    selectOption.value = etudiant.idOption;

    // Enregistrer le changement d'option
    selectOption.addEventListener('change', () => {
        appelAjax({
            url: 'ajax/modifieroption.php',
            data: {
                primarykey: etudiant.id,
                columns: {
                    idOption: selectOption.value
                }
            },
            success: (data) => {
                afficherToast(data.message);
            }
        });
    });

    // Identifier la ligne par l'identifiant de l'étudiant
    tr.id = etudiant.id;

    return tr;
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

lesLignes.replaceChildren();

for (const etudiant of lesEtudiants) {
    lesLignes.appendChild(creerLigneEtudiant(etudiant));
}
