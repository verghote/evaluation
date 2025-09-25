"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {getAge} from '/composant/fonction/date.js';

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global autoComplete, lesEtudiants */

const nomR = document.getElementById('nomR');
const id = document.getElementById('id');
const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');
const libelleCourt = document.getElementById('libelleCourt');
const age = document.getElementById('age');
const photo = document.getElementById('photo');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// Efface le champ de saisie quand celui-ci est sélectionné ainsi que les balises output
nomR.onfocus = function() {
    // Efface les valeurs des champs 'output'
    document.querySelectorAll('output').forEach(el => el.innerHTML = '');
    this.value = ''; // Efface le champ de saisie
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

// Lancement de la recherche
function rechercher(id) {
    appelAjax({
        url: 'ajax/getbyid.php',
        data: {id: id },
        success: (data) => afficher(data)
    });
}

function afficher(etudiant) {
    nom.value = etudiant.nom;
    prenom.innerText = etudiant.prenom;
    sexe.innerText = etudiant.sexe;
    dateNaissance.innerText = etudiant.dateNaissanceFr;
    age.innerText = getAge(etudiant.dateNaissanceFr) + ' ans';
    libelleCourt.innerText = etudiant.libelleCourt;
    // photo
    const img = document.createElement('img');
    img.style.maxHeight = '100%';
    img.style.maxWidth = '100%';
    img.style.objectFit = 'contain';
    img.style.display = 'block';
    img.style.margin = '0 auto'; // centrer horizontalement

    if (etudiant.present) {
        img.src = '/data/photo/' + etudiant.photo;
        img.alt = `${etudiant.nom} logo`;

    } else {
        img.src = '/data/photo/0.png';
        img.alt = `photo par défault`;
    }
    photo.innerHTML = ''; // vide le contenu précédent
    photo.appendChild(img);
    nomR.blur(); // retire le focus du champ de saisie
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// paramétrage du composant autoComplete
const options = {
    data: {
        src: lesEtudiants,
        keys: ["nomPrenom"]
    },
    selector: "#nomR",
    resultItem: {
        highlight: true,
    },
    theme: 'round',
    resultsList: {
        element: (list, data) => {
            const info = document.createElement("p");
            info.style.padding = "2px 2px";
            info.style.fontStyle = "italic";
            info.style.fontSize = "0.8em";
            const nb = data.matches.length;
            if (nb > 1) {
                info.innerHTML = nb + " étudiants trouvés";
            } else if (nb === 1) {
                info.innerHTML = "Un étudiant correspondant";
            } else {
                info.innerHTML = "Aucun étudiant correspondant";
            }
            list.append(info);
        },
        noResults: true,
        maxResults: 10,
    },
    events: {
        input: {
            // lorsque l'utilisateur clique sur un élément de la liste affichée
            selection: (event) => {
                const selection = event.detail.selection.value;
                nomR.value = selection.nomPrenom;
                rechercher(selection.id);
            },
        }
    },
};
new autoComplete(options);