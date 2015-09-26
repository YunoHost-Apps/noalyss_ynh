begin;
CREATE OR REPLACE FUNCTION comptaproc.jrnx_ins()
  RETURNS trigger AS
$BODY$
declare
n_fid bigint;
begin

NEW.j_tech_per := comptaproc.find_periode(to_char(NEW.j_date,'DD.MM.YYYY'));
if NEW.j_tech_per = -1 then
	raise exception 'Période invalide';
end if;

if NEW.j_qcode is NULL then
   return NEW;
end if;

NEW.j_qcode=trim(upper(NEW.j_qcode));

if length (NEW.j_qcode) = 0 then
    NEW.j_qcode=NULL;
    else
   select f_id into n_fid from fiche_detail  where ad_id=23 and ad_value=NEW.j_qcode;
	if NOT FOUND then
		raise exception 'La fiche dont le quick code est % n''existe pas',NEW.j_qcode;
	end if;
end if;
NEW.f_id:=n_fid;
return NEW;
end;
$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION comptaproc.jrn_check_periode()
  RETURNS trigger AS
$BODY$
declare
bClosed bool;
str_status text;
ljr_tech_per jrn.jr_tech_per%TYPE;
ljr_def_id jrn.jr_def_id%TYPE;
lreturn jrn%ROWTYPE;
begin
if TG_OP='UPDATE' then
	ljr_tech_per :=OLD.jr_tech_per ;
	NEW.jr_tech_per := comptaproc.find_periode(to_char(NEW.jr_date,'DD.MM.YYYY'));
	ljr_def_id   :=OLD.jr_def_id;
	lreturn      :=NEW;
	if NEW.jr_date = OLD.jr_date then
		return NEW;
	end if;
	if comptaproc.is_closed(NEW.jr_tech_per,NEW.jr_def_id) = true then
	      	raise exception 'Periode fermee';
	end if;
end if;

if TG_OP='INSERT' then
	NEW.jr_tech_per := comptaproc.find_periode(to_char(NEW.jr_date,'DD.MM.YYYY'));
	ljr_tech_per :=NEW.jr_tech_per ;
	ljr_def_id   :=NEW.jr_def_id;
	lreturn      :=NEW;
end if;

if TG_OP='DELETE' then
	ljr_tech_per :=OLD.jr_tech_per;
	ljr_def_id   :=OLD.jr_def_id;
	lreturn      :=OLD;
end if;

if comptaproc.is_closed (ljr_def_id,ljr_def_id) = true then
   	raise exception 'Periode fermee';
end if;

return lreturn;
end;$BODY$
  LANGUAGE 'plpgsql';

create or replace function comptaproc.is_closed (p_periode jrn.jr_tech_per%TYPE,p_jrn_def_id jrn.jr_def_id%TYPE)
returns bool as 
$BODY$
declare
bClosed bool;
str_status text;
begin
-- return true is the periode is closed otherwise false
select p_closed into bClosed from parm_periode
	where p_id=p_periode;

if bClosed = true then
	return bClosed;
end if;

select status into str_status from jrn_periode
       where p_id =p_periode and jrn_def_id=p_jrn_def_id;

if str_status <> 'OP' then
   return bClosed;
end if;
return false;
end;
$BODY$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION comptaproc.find_periode(p_date text)
  RETURNS integer AS
$BODY$

declare n_p_id int4;
begin

select p_id into n_p_id
	from parm_periode
	where
		p_start <= to_date(p_date,'DD.MM.YYYY')
		and
		p_end >= to_date(p_date,'DD.MM.YYYY');

if NOT FOUND then
	return -1;
end if;

return n_p_id;

end;$BODY$
  LANGUAGE plpgsql;


DROP TRIGGER t_check_jrn ON jrn;

CREATE TRIGGER t_check_jrn
  BEFORE INSERT OR DELETE OR update
  ON jrn
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.jrn_check_periode();


CREATE TABLE jrn_note
(
  n_id  serial,
  n_text text,
  jr_id bigint NOT NULL,
  CONSTRAINT jrnx_note_pkey PRIMARY KEY (n_id),  CONSTRAINT jrnx_note_j_id_fkey FOREIGN KEY (jr_id)      REFERENCES jrn (jr_id) MATCH SIMPLE      ON UPDATE CASCADE ON DELETE CASCADE);

COMMENT ON TABLE jrn_note IS 'Note about operation';
ALTER TABLE forecast ADD COLUMN f_start_date bigint;
ALTER TABLE forecast ADD COLUMN f_end_date bigint;
ALTER TABLE forecast
  ADD CONSTRAINT forecast_f_end_date_fkey FOREIGN KEY (f_end_date)
      REFERENCES parm_periode (p_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL;
ALTER TABLE forecast
  ADD CONSTRAINT forecast_f_start_date_fkey FOREIGN KEY (f_start_date)
      REFERENCES parm_periode (p_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL;
CREATE INDEX fki_f_start_date
  ON forecast
  USING btree
  (f_start_date);
CREATE INDEX fki_f_end_date
  ON forecast
  USING btree
  (f_end_date);


CREATE OR REPLACE FUNCTION comptaproc.jrn_add_note(p_jrid bigint, p_note text)
  RETURNS void AS
$BODY$
declare
	tmp bigint;
begin
	if length(trim(p_note)) = 0 then
	   delete from jrn_note where jr_id= p_jrid;
	   return;
	end if;
	
	select n_id into tmp from jrn_note where jr_id = p_jrid;
	
	if FOUND then
	   update jrn_note set n_text=trim(p_note) where jr_id = p_jrid;
	else 
	   insert into jrn_note (jr_id,n_text) values ( p_jrid, p_note);

	end if;
	
	return;
end;
$BODY$  LANGUAGE plpgsql ;


delete from action_gestion where f_id_dest != 0 and f_id_dest not in (select f_id from fiche);

CREATE OR REPLACE FUNCTION comptaproc.card_after_delete()
  RETURNS trigger AS
$BODY$

begin

	delete from action_gestion where f_id_dest = OLD.f_id;
	return OLD;

end;
$BODY$
LANGUAGE plpgsql ;

CREATE TRIGGER remove_action_gestion
  AFTER DELETE
  ON fiche
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.card_after_delete();

update version set val=92;
commit;