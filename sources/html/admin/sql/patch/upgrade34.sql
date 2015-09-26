begin;

CREATE or replace FUNCTION t_jrn_def_sequence() RETURNS "trigger"
    AS $$
declare
nCounter integer;

    BEGIN
    select count(*) into nCounter 
       from pg_class where relname='s_jrn_'||NEW.jrn_def_id;
       if nCounter = 0 then
       	   execute  'create sequence s_jrn_'||NEW.jrn_def_id;
	   raise notice 'Creating sequence s_jrn_%',NEW.jrn_def_id;
	 end if;

        RETURN NEW;
    END;
$$
    LANGUAGE plpgsql;

create or replace function correct_sequence_jrn () returns void
as $$
declare
	nCounter integer;
	nJrn_id record;
begin
	for nJrn_id in select jrn_Def_id from jrn_def loop
	    select count(*) into nCounter 
       	    	   from pg_class where relname='s_jrn_'||nJrn_id.jrn_def_id;
            if nCounter = 0 then
         	   execute  'create sequence s_jrn_'||nJrn_id.jrn_def_id;
	          raise notice 'Creating sequence s_jrn_%',nJrn_id.jrn_def_id;
	     end if;


	end loop;
end;
$$
	LANGUAGE plpgsql;
select correct_sequence_jrn();

drop function correct_sequence_jrn();



CREATE OR REPLACE FUNCTION tva_delete(int4)
  RETURNS void AS
$BODY$ 
declare
	p_tva_id alias for $1;
	nCount integer;
begin
	nCount=0;
	select count(*) into nCount from quant_sold where qs_vat_code=p_tva_id;
	if nCount != 0 then
                 return;
		
	end if;
	select count(*) into nCount from quant_purchase where qp_vat_code=p_tva_id;
	if nCount != 0 then
                 return;
		
	end if;

delete from tva_rate where tva_id=p_tva_id;
	return;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

update version set val=35;
commit;