begin;
update attr_def set ad_text='Compte bancaire' where ad_id=3;
ALTER TABLE mod_payment  DROP CONSTRAINT mod_payment_mp_fd_id_fkey ;
ALTER TABLE mod_payment  ADD CONSTRAINT mod_payment_mp_fd_id_fkey FOREIGN KEY (mp_fd_id)      REFERENCES fiche_def (fd_id) MATCH SIMPLE      ON UPDATE cascade ON DELETE cascade;
ALTER TABLE mod_payment  DROP CONSTRAINT mod_payment_mp_jrn_def_id_fkey ;
ALTER TABLE mod_payment  ADD CONSTRAINT mod_payment_mp_jrn_def_id_fkey FOREIGN KEY (mp_jrn_def_id)      REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE ON UPDATE 	CASCADE ON DELETE CASCADE;
ALTER TABLE fiche_def ADD COLUMN fd_description text;
update fiche_def set fd_description='Achats de marchandises' where fd_id=1;
update fiche_def set fd_description='Catégorie qui contient la liste des clients' where fd_id=2;
update fiche_def set fd_description='Catégorie qui contient la liste des comptes financiers: banque, caisse,...' where fd_id=3;
update fiche_def set fd_description='Catégorie qui contient la liste des fournisseurs' where fd_id=4;
update fiche_def set fd_label='Services & Biens Divers',fd_description='Catégorie qui contient la liste des charges diverses' where fd_id=5;
update fiche_def set fd_description='Catégorie qui contient la liste des prestations, marchandises... que l''on vend ' where fd_id=6;

update jrn_def set jrn_deb_max_line=5 where jrn_deb_max_line is null;

CREATE OR REPLACE FUNCTION comptaproc.periode_exist(p_date text,p_periode_id bigint)
 RETURNS integer
AS $function$

declare n_p_id int4;
begin

select p_id into n_p_id
        from parm_periode
        where
                p_start <= to_date(p_date,'DD.MM.YYYY')
                and
                p_end >= to_date(p_date,'DD.MM.YYYY')
                and
                p_id <> p_periode_id;

if NOT FOUND then
        return -1;
end if;

return n_p_id;

end;$function$
 LANGUAGE plpgsql;

create or replace function comptaproc.check_periode () returns trigger
as
$$
declare 
  nPeriode int;
begin
if periode_exist(to_char(NEW.p_start,'DD.MM.YYYY'),NEW.p_id) <> -1 then
       nPeriode:=periode_exist(to_char(NEW.p_start,'DD.MM.YYYY'),NEW.p_id) ;
        raise info 'Overlap periode start % periode %',NEW.p_start,nPeriode;
	return null;
end if;

if periode_exist(to_char(NEW.p_end,'DD.MM.YYYY'),NEW.p_id) <> -1 then
	nPeriode:=periode_exist(to_char(NEW.p_start,'DD.MM.YYYY'),NEW.p_id) ;
        raise info 'Overlap periode end % periode %',NEW.p_end,nPeriode;
	return null;
end if;
return NEW;
end;
$$ language plpgsql
;


create trigger parm_periode_check_periode_trg before update or insert on parm_periode for each row execute procedure check_periode();
update version set val=107;

commit;

