begin;

CREATE OR REPLACE FUNCTION comptaproc.jrnx_letter_del()
  RETURNS trigger AS
$BODY$
declare
row jrnx%ROWTYPE;
begin
row:=OLD;
-- remove orphan
delete from jnt_letter 
	where (jl_id in (select jl_id from letter_deb) and jl_id not in(select jl_id from letter_cred )) 
		or (jl_id not in (select jl_id from letter_deb  ) and jl_id  in(select jl_id from letter_cred ));
return row;
end;
$BODY$
  LANGUAGE 'plpgsql';

delete from jnt_letter where (jl_id in (select jl_id from letter_deb ) and jl_id not in(select jl_id from letter_cred )) or (jl_id not in (select jl_id from letter_deb ) and jl_id  in(select jl_id from letter_cred ));

-- Function: comptaproc.jrnx_del()

CREATE TRIGGER t_letter_del
  AFTER DELETE
  ON jrnx
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.jrnx_letter_del();
COMMENT ON TRIGGER t_letter_del ON jrnx IS 'Delete the lettering for this row';

update version set val=80;

commit;