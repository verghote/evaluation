Drop database if exists etudiant;

-- collation utf8mb4_0900_ai_ci only available in MySQL 8.0+ insensible aux accents et à la casse
create database etudiant character set utf8mb4 collate utf8mb4_0900_ai_ci;

SET default_storage_engine = InnoDb;

use etudiant;

create table options
(
    id           char(1)     not null,
    libelleCourt char(4)     not null,
    libelleLong  varchar(50) not null,
    primary key (id)
);

insert into options(id, libelleCourt, libelleLong)
values ('A', 'SISR', 'Solutions d''Infrastructure, Systèmes et Réseaux'),
       ('B', 'SLAM', 'Solutions Logicielles et Applications Métiers'),
       ('T', 'TC', 'Tronc commun premier semestre');

create table etudiant
(
    id            int auto_increment not null,
    nom           varchar(20)        not null,
    prenom        varchar(20)        NOT NULL,
    sexe	      char(1)			 NOT NULL default 'M',
    dateNaissance date               not null,
    idOption      char(1)            NOT NULL,
    photo         varchar(50)        null,
    primary key (id),
    unique (nom, prenom),
    foreign key (idOption) references options (id)
);


INSERT INTO etudiant (id, nom, prenom, sexe, dateNaissance, idOption, photo)
VALUES
    (1, 'BALDE', 'Aissatou Loundou', 'F', '2006-12-11', 'A', 'balde_aissatou_loundou.jpg'),
    (2, 'BOILET', 'Kameron', 'M', '2006-05-06', 'A', 'boilet_kameron.jpg'),
    (3, 'BOULLY', 'Alexandre', 'M', '2006-12-21', 'B', 'boully_alexandre.jpg'),
    (4, 'CAUET', 'Jules', 'M', '2003-06-16', 'A', 'cauet_jules.jpg'),
    (5, 'CAZIN', 'Tom', 'M', '2006-06-05', 'B', 'cazin_tom.jpg'),
    (6, 'DIANI', 'Ismael', 'M', '2004-10-13', 'B', 'diani_ismael.jpg'),
    (7, 'DUMONT', 'Hugo', 'M', '2006-04-30', 'B', 'dumont_hugo.jpg'),
    (8, 'DUPRESSOIR', 'Mathieu', 'M', '2003-10-31', 'B', 'dupressoir_mathieu.jpg'),
    (9, 'DUPUIS', 'Thomas', 'M', '2006-05-28', 'A', 'dupuis_thomas.jpg'),
    (10, 'EBELLE', 'MAKONGUE Jean-pascal', 'M', '2003-06-14', 'A', 'ebelle_makongue_jean-pascal.jpg'),
    (11, 'FOULON', 'Mathis', 'M', '2007-07-29', 'B', 'foulon_mathis.jpg'),
    (12, 'GARNIER', 'Kyllian', 'M', '2006-11-20', 'B', 'garnier_kyllian.jpg'),
    (13, 'HERNU', 'Valentin', 'M', '2006-03-15', 'B', 'hernu_valentin.jpg'),
    (14, 'KARACA', 'Atilla', 'M', '2005-04-27', 'A', 'karaca_atilla.jpg'),
    (15, 'LOEMBA', 'Guy-landry', 'M', '2006-03-07', 'A', NULL),
    (16, 'MARGOTIN', 'Paul', 'M', '2005-12-11', 'A', 'margotin_paul.jpg'),
    (17, 'MERCIER', 'Alexi', 'M', '2005-01-17', 'B', 'mercier_alexi.jpg'),
    (18, 'MERVILLE', 'Lucas', 'M', '2006-01-09', 'A', 'merville_lucas.jpg'),
    (19, 'MORTELETTE', 'Clément', 'M', '2006-01-19', 'A', 'mortelette_clement.jpg'),
    (20, 'NOUHI', 'Marwan', 'M', '2004-09-10', 'A', 'nouhi_marwan.jpg'),
    (21, 'PAYET', 'Théo', 'M', '2005-09-09', 'A', 'payet_theo.jpg'),
    (22, 'POINTIN', 'Samson', 'M', '2005-02-01', 'A', 'pointin_samson.jpg'),
    (23, 'ROUSSELLE', 'Etienne', 'M', '2007-03-27', 'A', 'rousselle_etienne.jpg'),
    (24, 'VASSEUR', 'Lorenzo', 'M', '2004-03-06', 'A', 'vasseur_lorenzo.jpg'),
    (25, 'YILDIZ', 'Muhammedali', 'M', '2004-05-13', 'B', 'yildiz_muhammedali.jpg'),
    (26, 'ZON', 'Jeremy', 'M', '2006-02-25', 'B', 'zon_jeremy.jpg');
