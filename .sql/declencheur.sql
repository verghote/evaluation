use etudiant;

drop trigger if exists avantAjoutEtudiant;

create trigger avantAjoutEtudiant
    before insert
    on etudiant
    for each row
begin
    set new.nom = upper(new.nom);
    if exists(select 1 from etudiant where nom = new.nom and prenom = new.prenom) then
        SIGNAL sqlstate '45000' set message_text = '~Cet étudiant existe déjà';
    end if;
    set @dateMax =  CURDATE() - INTERVAL 17 YEAR;
    set @dateMin = CURDATE() - INTERVAL 25 YEAR;
    if new.dateNaissance not between @dateMin and  @dateMax then
        SIGNAL sqlstate '45000' set message_text = '~Cette personne n\'a pas l\'âge requis pour être étudiant';
    end if;
end;

drop trigger if exists avantModificationEtudiant;
create trigger avantModificationEtudiant
    before update
    on etudiant
    for each row
begin
    set new.nom = upper(new.nom);
    if exists(select 1 from etudiant where nom = new.nom and prenom = new.prenom and id <> new.id) then
        SIGNAL sqlstate '45000' set message_text = '~Cet étudiant existe déjà';
    end if;
    set @dateMax =  CURDATE() - INTERVAL 17 YEAR;
    set @dateMin = CURDATE() - INTERVAL 25 YEAR;
    if new.dateNaissance not between @dateMin and  @dateMax then
        SIGNAL sqlstate '45000' set message_text = '~Cette personne n\'a pas l\'âge requis pour être étudiant';
    end if;
end;