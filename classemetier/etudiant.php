<?php
declare(strict_types=1);

class Etudiant extends Table
{
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

        // définition des colonnes modifiables en mode colonne
        $this->listOfColumns->Values = ['idOption'];

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
     * @param string $id
     * @return array | false
     */
    public static function getById(string $id): array|false
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
}