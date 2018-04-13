begin;

CREATE OR REPLACE FUNCTION jrn_check_periode()
  RETURNS "trigger" AS
$BODY$
declare 
bClosed bool;
str_status text;
ljr_tech_per jrn.jr_tech_per%TYPE;
ljr_def_id jrn.jr_def_id%TYPE;
lreturn jrn%ROWTYPE;
begin
if TG_OP='INSERT' then
	ljr_tech_per :=NEW.jr_tech_per;
	ljr_def_id   :=NEW.jr_def_id;
        lreturn      :=NEW;
end if;

if TG_OP='DELETE' then
	ljr_tech_per :=OLD.jr_tech_per;
	ljr_def_id   :=OLD.jr_def_id;
        lreturn      :=OLD;
end if;

select p_closed into bClosed from parm_periode 
	where p_id=ljr_tech_per;

if bClosed = true then
	raise exception 'Periode fermee';
end if;

select status into str_status from jrn_periode 
       where p_id =ljr_tech_per and jrn_def_id=ljr_def_id;

if str_status <> 'OP' then
	raise exception 'Periode fermee';
end if;

return lreturn;
end;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
update version set val=38;
commit;
