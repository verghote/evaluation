"use strict";
// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {retournerVersApresConfirmation } from "/composant/fonction/afficher.js";
import {configurerFormulaire, configurerDate, donneesValides, filtrerLaSaisie, enleverAccent, supprimerEspace} from "/composant/fonction/formulaire.js";
import {ucFirst} from '/composant/fonction/format.js';

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesOptions, dateMin, dateMax */

const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');
const idOption = document.getElementById('idOption');
const msg = document.getElementById('msg');
const btnAjouter = document.getElementById('btnAjouter');


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

    //  demande d'ajout dans la base de données
    appelAjax({
        url: '/ajax/ajouter.php',
        data: formData,
        success: () => retournerVersApresConfirmation("Etudiant ajouté", '/liste')
    });
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
    min:dateMin,
    max: dateMax,
    valeur: dateMax
});


// Données de test
nom.value = 'Dupont';
prenom.value = 'Hervé';
dateNaissance.value = '2005-10-01';
