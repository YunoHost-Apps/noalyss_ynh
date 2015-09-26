begin;
CREATE OR REPLACE FUNCTION find_pcm_type(pp_value "numeric")
  RETURNS text AS
$BODY$
declare
        str_type text;
        str_value text;
        n_value numeric;
        nLength integer;
begin 
        str_value:=trim(to_char(pp_value,'99999999999999999999999999999'));
        nLength:=length(str_value);
	while nLength > 0 loop
		n_value:=to_number(str_value,'99999999999999999999999999999');
      		select p_type into str_type from parm_poste where p_value=n_value;
		if FOUND then
			return str_type;
		end if;
		nLength:=nLength-1;
		str_value:=substring(str_value from 1 for nLength);	
	end loop;
return 'CON';
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

update version set val=39;
commit;	
