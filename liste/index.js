"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------
import {ucWord} from "/composant/fonction/format.js";
import {activerTri} from "/composant/fonction/tableau.js";
import {getTd, getTdWithImg, getTr} from "/composant/fonction/trtd.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global data  */

const lesLignes = document.getElementById('lesLignes');

// Propriété de l'objet data : licence, nom, prenom, sexe, dateNaissanceFr, idCategorie, nomClub
// Colonne du tableau : Licence, Nom, Prénom, Sexe, Date de naissance, Catégorie, Club

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Affichage des étudiants dans le tableau
 * @param data
 */
function afficher(data) {
    lesLignes.innerHTML = ""; // Efface les lignes précédentes

    for (const etudiant of data) {
        // Liste des cellules
        const lesTds = [
            getTd(ucWord(etudiant.nomPrenom)),
            getTd(etudiant.sexe, { centrer: true, masquer: true }),
            getTd(etudiant.dateNaissanceFr, { centrer: true }),
            getTd(etudiant.libelleCourt, { centrer: true })
        ];

        // Cellule photo ou "Non prise"
        if (etudiant.present) {
            lesTds.push(
                getTdWithImg(
                    '/data/photo/' + etudiant.photo,
                    'Photo de ' + etudiant.nomPrenom,
                    { size: 40, radius: '50%', masquer: true }
                )
            );
        } else {
            lesTds.push(
                getTd("Non prise", { centrer: true, masquer: true })
            );
        }

        // Création de la ligne <tr> avec les cellules
        const tr = getTr(lesTds);

        // Ajout de la ligne dans le tbody
        lesLignes.appendChild(tr);
    }
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

afficher(data);

// Activer le tri sur les colonnes
activerTri({
    idTable: "leTableau",
    getData: () => data,
    afficher: afficher,
    triInitial: {
        colonne: 'nomPrenom',
        ordre: "asc"
    }
});