<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Page;
use ClasseTechnique\Config;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Photos des étudiants")
    ->setDonnee('lesEtudiants', Etudiant::getAll())
    ->setDonnee('lesParametres', Config::chargerPhp('etudiant'))
    ->setDonnee('repertoire', substr(DOSSIER_PHOTO_ETUDIANT, strlen(DOSSIER_WWW)))
    ->avecJeton()
    ->afficher();


