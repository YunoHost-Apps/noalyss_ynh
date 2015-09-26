begin;



CREATE or replace FUNCTION t_document_modele_validate() RETURNS "trigger"
    AS $$
declare 
    lText text;
    modified document_modele%ROWTYPE;
begin
    modified=NEW;

	modified.md_filename=replace(NEW.md_filename,' ','_');
	return modified;
end;
$$ LANGUAGE plpgsql;



CREATE or replace FUNCTION t_document_validate() RETURNS "trigger"
    AS $$
declare
  lText text;
  modified document%ROWTYPE;
begin
    	modified=NEW;
	modified.d_filename=replace(NEW.d_filename,' ','_');
	return modified;
end;
$$ LANGUAGE plpgsql;


CREATE TRIGGER document_validate
    BEFORE INSERT OR UPDATE ON document
    FOR EACH ROW
    EXECUTE PROCEDURE t_document_validate();

CREATE TRIGGER document_modele_validate
    BEFORE INSERT OR UPDATE ON document_modele
    FOR EACH ROW
    EXECUTE PROCEDURE t_document_modele_validate();

update operation_analytique set oa_debit=j_debit from jrnx where jrnx.j_id=operation_analytique.j_id ;
update version set val=36;
commit;
