"use strict";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------
/* global lesEtudiants */

const lesCartes = document.getElementById('lesCartes');

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

    const img = document.createElement('img');

    img.style.maxHeight = '100%';
    img.style.maxWidth = '100%';
    img.style.objectFit = 'contain';
    img.style.display = 'block';
    img.style.margin = '0 auto'; // centrer horizontalement

    if (element.present) {
        img.src = '/data/photo/' + element.photo;
        img.alt = `${element.nom} logo`;

    } else {
        img.src = '/data/photo/0.png';
        img.alt = `photo par défault`;
    }
    corps.appendChild(img);
    // Pied de la carte
    const pied = document.createElement('div');
    pied.classList.add('card-footer', 'text-muted', 'text-center');
    pied.innerText = element.libelleCourt;

    carte.appendChild(entete);
    carte.appendChild(corps);
    carte.appendChild(pied);

    carte.style.flexGrow = '0';          // La carte ne grandit pas au-delà de sa base
    carte.style.flexShrink = '1';        // Elle peut se rétrécir si l'espace est limité
    carte.style.flexBasis = '240px';     // Largeur de base (taille cible)
    carte.style.maxWidth = '100%';       // Elle ne dépasse jamais la largeur de son conteneur

    return carte;
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

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

