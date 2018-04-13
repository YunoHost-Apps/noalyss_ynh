begin;

alter table jrn_def add jrn_def_description text;

CREATE OR REPLACE FUNCTION comptaproc.t_jrn_def_description()
 RETURNS trigger
AS $function$
    declare
        str varchar(200);
    BEGIN
        str := substr(NEW.jrn_def_description,1,200);
        NEW.jrn_def_description := str;

        RETURN NEW;
    END;
$function$
 LANGUAGE plpgsql
;
create  trigger jrn_def_description_ins_upd before insert or update on jrn_def for each row execute procedure comptaproc.t_jrn_def_description();

update jrn_def set jrn_def_description='Concerne tous les achats, factures reçues, notes de crédit reçues et notes de frais' where jrn_def_id=3;
update jrn_def set jrn_def_description='Concerne tous les mouvements financiers (comptes en banque, caisses, visa...)' where jrn_def_id=1;
update jrn_def set jrn_def_description='Concerne toutes les opérations comme les amortissements, les comptes TVA, ...' where jrn_def_id=4;
update jrn_def set jrn_def_description='Concerne toutes les ventes, notes de crédit envoyées' where jrn_def_id=2;

alter table document add d_description text;

update document set d_description = d_filename;

update version set val=110;

commit;