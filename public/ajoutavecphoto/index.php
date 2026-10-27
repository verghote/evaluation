<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseMetier\Options;
use ClasseTechnique\Config;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// Chargement des paramètres d'upload
$lesParametres = Config::chargerPhp('etudiant');

// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Ajout d'un étudiant")
    ->setDonnee('lesEtudiants', Etudiant::getAll())
    ->setDonnee("lesOptions", Options::getLesOptions())
    ->setDonnee('lesParametres', $lesParametres)
    ->avecJeton()
    ->afficher();



