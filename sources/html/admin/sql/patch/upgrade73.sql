begin;
DROP FUNCTION if exists comptaproc.get_action_tree();

CREATE OR REPLACE FUNCTION comptaproc.action_get_tree(p_id bigint)
  RETURNS setof bigint AS
$BODY$

declare
   e bigint;
   i bigint;
begin
   for e in select ag_id from action_gestion where ag_ref_ag_id=p_id
   loop
	if e = 0 then 
		return;
	end if;
	return next e;
	for i in select ag_id from action_gestion where ag_ref_ag_id=e
	loop
	if i = 0 then 
		return;
	end if;
		return next i;
	end loop;
   end loop;
   return;

end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

update version set val=74;
commit;