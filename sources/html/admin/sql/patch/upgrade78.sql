begin;

DROP FUNCTION if exists comptaproc.action_get_tree(bigint);

CREATE OR REPLACE FUNCTION comptaproc.action_get_tree(p_id bigint)
  RETURNS setof bigint AS
$BODY$

declare
   e bigint;
   i bigint;
begin
   for e in select ag_id from action_gestion where ag_ref_ag_id=p_id
   loop
        for i in select action_get_tree from  comptaproc.action_get_tree(e)
        loop
                raise notice ' == i %', i;
                return next i;
        end loop;
    raise notice ' = e %', e;
    return next e;
   end loop;
   return;

end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

update version set val=79;

commit;
