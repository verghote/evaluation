<?php
declare(strict_types=1);

class Etudiant extends Table
{

    /**
     * Configuration intégrée pour l'upload des logos.
     */
    private const CONFIG = [
        'repertoire' => '/data/photo',
        'extensions' => ["jpg", "png"],
        'types' => ["image/pjpeg", "image/jpeg", "x-png", "image/png"],
        'maxSize' => 50 * 1024,
        'require' => false,
        'rename' => true,
        'sansAccent' => true,
        'redimensionner' => false,
        'height' => 150,
        'width' => 150,
        'accept' => '.jpg, .png',
        'label' => '(50 Ko max, 150*150 max, jpg ou png)'
    ];

    private const DIR = RACINE . '/data/photo/';

    public function __construct()
    {
        parent::__construct('etudiant');

        // colonne nom
        // Que des lettres et l'espace en séparateur en majuscule sans espace superflu
        // contenir 20 caractères
        $input = new inputText();
        $input->Require = true;
        $input->Casse = 'U';
        $input->SupprimerAccent = true;
        $input->SupprimerEspaceSuperflu = true;
        $input->Pattern = "^[a-zA-Z]+([' \-]?[a-zA-Z]+)*$";
        $input->MaxLength = 20;
        $this->columns['nom'] = $input;

        // colonne prénom
        $input = new inputText();
        $input->Require = true;
        $input->SupprimerAccent = false;
        $input->SupprimerEspaceSuperflu = true;
        $input->Pattern = "^[a-zA-ZÀ-ÿÂ-üçÇ]+([ '\-][a-zA-ZÀ-ÿÂ-üçÇ]+)*$";
        $input->MaxLength = 20;
        $this->columns['prenom'] = $input;

        // colonne dateNaissance
        $input = new InputDate();
        $input->Require = true;
        $input->Max = date('Y-m-d', strtotime('-17 year'));
        $input->Min = date('Y-m-d', strtotime('-25 year'));
        $this->columns['dateNaissance'] = $input;

        // colonne idOption
        $input = new InputList();
        $this->columns['idOption'] = $input;
        $curseur = new Select();
        $sql = 'select id from options order by id;';
        $lesLignes = $curseur->getRows($sql);
        // stockage des id dans un tableau
        foreach ($lesLignes as $ligne) {
            $input->Values[] = $ligne['id'];
        }

        // colonne  photo
        $input = new InputText();
        $input->Require = false;
        $this->columns['photo'] = $input;

        // définition des colonnes modifiables en mode colonne
        $this->listOfColumns->Values = ['idOption'];
    }


    /**
     * Renvoie la configuration des photos des étudiants
     * @return array<string, mixed>
     */
    public static function getConfig(): array
    {
        return self::CONFIG;
    }

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
            SELECT etudiant.id, nom, prenom , concat(nom, ' ' , prenom) as nomPrenom,
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
     * retourne l'identifiant et la concaténation nom et prénom et l'id de l'option de tous les étudiants
     * @return array
     */
    public static function getLesEtudiants(): array
    {
        $sql = <<<EOD
            SELECT id, concat(nom, ' ' , prenom) as nomPrenom, idOption
            FROM etudiant
            ORDER BY nomPrenom;
EOD;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * retourne ll'identifiant et la concaténation nom et prénom de tous les étudiants
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
     * @param int $id
     * @return array | false
     */
    public static function getById(int $id): array|false
    {
        $sql = <<<EOD
            SELECT etudiant.id, nom, prenom, concat(nom, ' ', prenom) as nomPrenom, dateNaissance,
                   date_format(dateNaissance, '%d/%m/%Y') as dateNaissanceFr, 
                   sexe, libelleCourt, photo 
            FROM etudiant
               join options on etudiant.idOption = options.id
            WHERE etudiant.id = :id;
EOD;
        $select = new Select();
        $ligne = $select->getRow($sql, ['id' => $id]);

        if ($ligne) {
            // ajout d'une propriété  'present' permettant de vérifier l'existence de la photo
            $ligne['present'] = !empty($ligne['photo']) && is_file(self::DIR . $ligne['photo']);
        }
        return $ligne ?: false;
    }


    /**
     * Retourne le nombre d'étudiants
     * @return int
     */
    public static function getLesOptions(): array
    {
        $sql = "SELECT id, libelleLong FROM options ORDER BY libelleLong;";
        $select = new Select();
        return $select->getRows($sql);
    }

    // ------------------------------------------------------------------------------------------------
    // Méthodes de mise à jour
    // ------------------------------------------------------------------------------------------------

    /**
     * Supprime une photo contenue dans le répertoire data/phto
     * @param string $photo
     * @return void
     */
    public static function supprimerPhoto(string $photo): void
    {
        $chemin = self::DIR . '/' . $photo;
        if (is_file($chemin)) {
            unlink($chemin);
        }
    }

    /**
     * Supprime un enregistrement de la table etudiant
     * @param int $id
     * @return void
     */
    public static function supprimer(int $id): void
    {
        $db = Database::getInstance();
        $sql = "delete from etudiant where id = :id;";
        $cmd = $db->prepare($sql);
        $cmd->bindValue('id', $id);
        try {
            $cmd->execute();
        } catch (Exception $e) {
            Erreur::traiterReponse($e->getMessage());
        }
    }



    /**
     * Met à jour la photo d'un étudiant
     * @param string $id
     * @param string $logo
     * @return void
     */
    public static function majPhoto(int $id, string $photo): void
    {
        $sql = "update etudiant set photo = :photo where id = :id;";
        $db = Database::getInstance();
        $cmd = $db->prepare($sql);
        $cmd->bindValue('id', $id);
        $cmd->bindValue('photo', $photo);
        try {
            $cmd->execute();
        } catch (Exception $e) {
            Erreur::traiterReponse($e->getMessage());
        }
    }
}