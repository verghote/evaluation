"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {afficherToast, messageBox} from '/composant/fonction/afficher.js';
import {
    configurerFormulaire,
    configurerDate,
    effacerLesErreurs,
    filtrerLaSaisie,
    donneesValides
} from "/composant/fonction/formulaire.js";
import {ucFirst, ucWord} from "/composant/fonction/format.js";
import {getAge} from '/composant/fonction/date.js';


// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------
/* global dateMin, dateMax, lesOptions, lesEtudiants, autoComplete  */

// récupération des éléments de l'interface
const formulaire = document.getElementById('formulaire');

const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');
const idOption = document.getElementById('idOption');

const nomR = document.getElementById('nomR');
const btnModifier = document.getElementById('btnModifier');
const btnSupprimer = document.getElementById('btnSupprimer');
const msg = document.getElementById('msg');

let etudiant = {}; // objet global contenant les informations sur l'étudiant sélectionné

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

nomR.onfocus = () => {
    nomR.value = '';
    formulaire.style.display = 'none';
};


btnModifier.onclick = () => {
    effacerLesErreurs();
    // mise en forme des données
    nom.value = nom.value.trim().toLocaleUpperCase('fr-FR');
    prenom.value = ucWord(prenom.value.trim());
    if (donneesValides()) {
        modifier();
    }
};

// Transformer immédiatement le nom en majuscule
nom.addEventListener('input', () => {
    const pos = nom.selectionStart;
    nom.value = nom.value.toLocaleUpperCase('fr-FR');
    nom.setSelectionRange(pos, pos);
});

prenom.addEventListener('input', () => {
    const pos = prenom.selectionStart;
    const dernierCaractere = prenom.value[pos - 1];

    // Ne rien faire si le dernier caractère est un espace, apostrophe ou tiret
    if ([' ', '\'', '-'].includes(dernierCaractere)) return;

    prenom.value = ucWord(prenom.value);
    prenom.setSelectionRange(pos, pos);
});

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

// Lancement de la recherche
function rechercher(id) {
    appelAjax({
        url: 'ajax/getbyid.php',
        data: {id: id},
        success: (data) => afficher(data)
    });
}

/**
 * affichage des coordonnées du coureur contenues dans le paramètre implicite data
 * On conserve les coordonnées du coureur dans l'objet coureur afin de détecter une modification
 * @param data
 */
function afficher(data) {
    // sauvegarde des données dans l'objet global etudiant afin de détecter les modifications
    etudiant = data;
    // affichage des données
    nom.value = etudiant.nom;
    prenom.value = etudiant.prenom;
    sexe.value = etudiant.sexe;
    dateNaissance.value = etudiant.dateNaissance;
    // calcul de l'âge
    age.innerText = getAge(etudiant.dateNaissance) + ' ans';
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

    // rétirer la mise en valeur des champs
    [nom, prenom, sexe, dateNaissance].forEach(champ => {
        champ.style.border = '';
        champ.style.color = '';
    });

    // afficher le formulaire
    formulaire.style.display = 'block';
}


function modifier() {
    msg.innerHTML = '';
    const lesValeurs = {};
    if (nom.value !== etudiant.nom) {
        lesValeurs.nom = nom.value;
    }
    if (prenom.value !== etudiant.prenom) {
        lesValeurs.prenom = prenom.value;
    }
    if (sexe.value !== etudiant.sexe) {
        lesValeurs.sexe = sexe.value;
    }
    if (dateNaissance.value !== etudiant.dateNaissance) {
        lesValeurs.dateNaissance = dateNaissance.value;
    }
    // Vérification : si aucune clé n’a été ajoutée => pas de modification
    if (Object.keys(lesValeurs).length === 0) {
        messageBox("Aucune modification constatée", 'info');
        return;
    }
    appelAjax({
        url: '/ajax/modifier.php',
        data: {
            table: 'etudiant',
            id: etudiant.id,
            lesValeurs : JSON.stringify(lesValeurs)
        },
        success: () => {
            afficherToast('Etudiant modifié', 'success')
            // Mettre en surbrillance les champs modifiés
            for (const cle in lesValeurs) {
                const champ = document.getElementById(cle);
                champ.style.border = '2px solid green';
                champ.style.color = 'green';
            }
            // mettre à jour l'objet etudiant
            for (const cle in lesValeurs) {
                etudiant[cle] = lesValeurs[cle];
            }
            // mettre à jour le nom affiché dans le champ de recherche
            nomR.value = etudiant.nom + ' ' + etudiant.prenom;

            // mettre à jour le tableau lesEtudiants alimentant la source de l'autocomplétion
            const index = lesEtudiants.findIndex(e => e.id === etudiant.id);
            lesEtudiants[index].nomPrenom = etudiant.nom + ' ' + etudiant.prenom;
        }
    })
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// intervalle sur la date de naissance
// La date doit être comprise entre dateMin et dateMax
configurerDate(dateNaissance, {
    min: dateMin,
    max: dateMax,
});


// paramétrage du composant autoComplete
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

// contrôle des données saisies toujours après le système d'autocomplétion qui vient ajouter ses propres balises
configurerFormulaire();
filtrerLaSaisie('nom',  /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/);
filtrerLaSaisie('nomR',  /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/);
filtrerLaSaisie('prenom', /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/);
