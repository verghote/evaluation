<?php
declare(strict_types=1);

return [
    // Taille maximale (150 Ko)
    'maxSize' => 150 * 1024,

    // Dimensions maximales
    'maxWidth' => 86,
    'maxHeight' => 128,

    // L'image doit-elle être redimensionnée si elle dépasse la largeur ou la hauteur maximale.
    'resized' => true,

    // Extensions autorisées
    'lesExtensions' => ['jpg', 'png'],

    // Types MIME autorisés
    'lesTypes' => ['image/jpeg', 'image/png'],

    // Si le fichier existe déjà, doit-il être renommé ?
    'renommerSiExiste' => false,
];
