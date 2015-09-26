begin;
alter SEQUENCE document_type_dt_id_seq start 25;

insert into document_type (dt_id,dt_value) values (20,'Réception commande Fournisseur');
insert into document_type (dt_id,dt_value) values (21,'Réception commande Client');
insert into document_type (dt_id,dt_value) values (22,'Réception magazine');

CREATE OR REPLACE FUNCTION extension_ins_upd()
  RETURNS "trigger" AS
$BODY$
declare
 sCode text;
 sFile text;
begin
sCode:=trim(upper(NEW.ex_code));
sCode:=replace(sCode,' ','_');
sCode:=substr(sCode,1,15);
sCode=upper(sCode);
NEW.ex_code:=sCode;
-- remove forbidden char
sFile:=NEW.ex_file;
sFile:=replace(sFile,';','_');
sFile:=replace(sFile,'<','_');
sFile:=replace(sFile,'>','_');
sFile:=replace(sFile,'..','');
sFile:=replace(sFile,'&','');
sFile:=replace(sFile,'|','');



return NEW;

end;

$BODY$
LANGUAGE 'plpgsql';

update version set val=61;

commit;
