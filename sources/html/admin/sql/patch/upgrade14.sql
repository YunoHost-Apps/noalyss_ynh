begin;
alter table import_tmp add column status varchar(1);
alter table import_tmp alter status set default 'n';
create or replace function trim_cvs_quote() returns trigger as $trim$
declare
        modified import_tmp%ROWTYPE;
begin
	modified:=NEW;
	modified.devise=replace(new.devise,'"','');
	modified.poste_comptable=replace(new.poste_comptable,'"','');
        modified.compte_ordre=replace(NEW.COMPTE_ORDRE,'"','');
        modified.detail=replace(NEW.DETAIL,'"','');
        modified.num_compte=replace(NEW.NUM_COMPTE,'"','');
	return modified;
end;
$trim$ language plpgsql;

update import_tmp set status = 't' where ok=true;
update import_tmp set status = 'n' where ok = false;
update import_tmp set status = 'n' where ok is null;

alter table import_tmp  add constraint chk_status check (status in ('n','w','d','t'));


alter table import_tmp drop column ok ;
comment on table import_tmp is 'Table temporaire pour l''importation des banques en format CSV';
comment on column import_tmp.status is 'Status doit être w pour en attente, t pour transfèrer ou d à effacer';


create or replace function trim_cvs_quote() returns trigger as $trim$
declare
        modified import_tmp%ROWTYPE;
begin
	modified:=NEW;
	modified.devise=replace(new.devise,'"','');
	modified.poste_comptable=replace(new.poste_comptable,'"','');
        modified.compte_ordre=replace(NEW.COMPTE_ORDRE,'"','');
        modified.detail=replace(NEW.DETAIL,'"','');
        modified.num_compte=replace(NEW.NUM_COMPTE,'"','');
	return modified;
end;
$trim$ language plpgsql;

update version set val=15;
commit;
