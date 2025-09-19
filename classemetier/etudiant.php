<?php
declare(strict_types=1);

class Etudiant
{
    private const DIR = RACINE . '/data/photo/';

    /**
     * retourne l'ensemble des informations sur les étudiants
     * @return array
     */
    public static function getAll(): array
    {
        // requête de sélection des étudiants
        // champs à retourner :
        //     nom, prenom, nomPrenom (concaténation du nom et prenom),
        //     dateNaissanceFr (date naissance dans le format français),
        //     sexe, libelleCourt (table options), photo';
        $sql = <<<EOD
            SELECT nom, prenom , concat(nom, ' ' , prenom) as nomPrenom,
                   date_format(dateNaissance, '%d/%m/%Y') as dateNaissanceFr, 
                   sexe, libelleCourt, photo 
            FROM etudiant
               join options on etudiant.idOption = options.id
            ORDER BY nom, prenom;
EOD;
        $select = new Select();
        $lesLignes = $select->getRows($sql);

        // ajout d'une colonne permettant de vérifier l'existence de la photo
        foreach ($lesLignes as &$ligne) {
            $ligne['present'] = !empty($ligne['photo']) && is_file(self::DIR . $ligne['photo']);
        }
        return $lesLignes;
    }

    /**
     * retourne l'ensemble des informations sur les coureurs
     * @return array
     */
    public static function getListe(): array
    {
        $sql = <<<EOD
            SELECT id, concat(nom, ' ' , prenom) as nomPrenom
            FROM etudiant
            ORDER BY nomPrenom;
EOD;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * Retourne les informations sur un étudiant
     * @param string $id
     * @return array | false
     */
    public static function getById(string $id): array|false
    {
        $sql = <<<EOD
            SELECT nom, prenom, concat(nom, ' ', prenom) as nomPrenom,
                   date_format(dateNaissance, '%d/%m/%Y') as dateNaissanceFr, 
                   sexe, libelleCourt, photo 
            FROM etudiant
               join options on etudiant.idOption = options.id
            WHERE etudiant.id = :id;
EOD;
        $select = new Select();
        $ligne = $select->getRow($sql, ['id' => $id]);

        if ($ligne) {
            // ajout d'une colonne permettant de vérifier l'existence de la photo
            $ligne['present'] = !empty($ligne['photo']) && is_file(self::DIR . $ligne['photo']);
        }
        return $ligne ?: false;
    }
}