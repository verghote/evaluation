<?php
if (!isset($titre)) {
    $titre = ucwords(trim(dirname($_SERVER['PHP_SELF']), '/'));
}

// Si on est à la racine, on cache le lien avec `display: none;`, sinon rien
$chemin = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
$style = (trim($chemin, '/') === '') ? 'none;' : 'block';

?>
<!-- Bloc de gauche : le titre -->
<div>
    <?= htmlspecialchars($titre) ?>
</div>
<!-- Bloc de droite : lien retour, caché si on est à la racine -->
<div style="">
    <a href="/" style="font-size: 1.25rem; color:white; display:<?= $style ?> ">↩</a>
</div>