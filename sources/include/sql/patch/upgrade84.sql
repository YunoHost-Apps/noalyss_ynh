begin;
update jrnx set j_qcode=null where trim(j_qcode)='';

CREATE OR REPLACE FUNCTION comptaproc.insert_jrnx(p_date character varying, p_montant numeric,
p_poste account_type, p_grpt integer, p_jrn_def integer, p_debit boolean, p_tech_user text, p_tech_per integer,
p_qcode text, p_comment text)
  RETURNS void AS
$BODY$
begin
	insert into jrnx
	(
		j_date,
		j_montant,
		j_poste,
		j_grpt,
		j_jrn_def,
		j_debit,
		j_text,
		j_tech_user,
		j_tech_per,
		j_qcode
	) values
	(
		to_date(p_date,'DD.MM.YYYY'),
		p_montant,
		p_poste,
		p_grpt,
		p_jrn_def,
		p_debit,
		p_comment,
		p_tech_user,
		p_tech_per,
		p_qcode
	);

return;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

CREATE OR REPLACE FUNCTION comptaproc.jrnx_ins()
  RETURNS trigger AS
$BODY$
declare
n_fid bigint;
begin

if NEW.j_qcode is NULL then
   return NEW;
end if;

NEW.j_qcode=trim(upper(NEW.j_qcode));

if length (NEW.j_qcode) = 0 then
    NEW.j_qcode=NULL;
    else
   select f_id into n_fid from fiche join jnt_fic_att_value using (f_id) join attr_value using(jft_id) where ad_id=23 and av_text=NEW.j_qcode;
	if NOT FOUND then 
		raise exception 'La fiche dont le quick code est % n''existe pas',NEW.j_qcode;
	end if;
end if;
return NEW;
end;
$BODY$
  LANGUAGE 'plpgsql' ;


CREATE TRIGGER t_jrnx_ins
  BEFORE INSERT 
  ON jrnx
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.jrnx_ins();
COMMENT ON TRIGGER t_jrnx_ins ON jrnx IS 'check that the qcode used by the card exists and format it : uppercase and trim the space';



CREATE TRIGGER t_jrnx_upd
  BEFORE UPDATE
  ON jrnx
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.jrnx_ins();
COMMENT ON TRIGGER t_jrnx_ins ON jrnx IS 'check that the qcode used by the card exists and format it : uppercase and trim the space';



update version set val=85;
commit;