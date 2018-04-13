begin;

CREATE TABLE del_action
(
  del_id serial NOT NULL,
  del_name text NOT NULL,
  del_time timestamp,
  CONSTRAINT del_action_pkey PRIMARY KEY (del_id)
) ;


CREATE TABLE del_jrn
(
  jr_id int4,
  jr_def_id int4,
  jr_montant numeric(20,4),
  jr_comment text,
  jr_date date,
  jr_grpt_id int4,
  jr_internal text,
  jr_tech_date timestamp,
  jr_tech_per int4,
  jrn_ech date,
  jr_ech date,
  jr_rapt text,
  jr_valid bool,
  jr_opid int4,
  jr_c_opid int4,
  jr_pj oid,
  jr_pj_name text,
  jr_pj_type text,
  del_jrn_date timestamp
) ;
ALTER TABLE del_jrn
  ADD CONSTRAINT jr_id PRIMARY KEY(jr_id);


CREATE TABLE del_jrnx
(
  j_id int4,
  j_date date,
  j_montant numeric(20,4),
  j_poste poste_comptable,
  j_grpt int4,
  j_rapt text,
  j_jrn_def int4,
  j_debit bool,
  j_text text,
  j_centralized bool,
  j_internal text,
  j_tech_user text,
  j_tech_date timestamp,
  j_tech_per int4,
  j_qcode text
) ;

ALTER TABLE del_jrnx
  ADD CONSTRAINT j_id PRIMARY KEY(j_id);

CREATE OR REPLACE FUNCTION jrn_del()
  RETURNS "trigger" AS
$BODY$
declare
row jrn%ROWTYPE;
begin
row:=OLD;
insert into del_jrn select *,now() from jrn where jr_id=row.jr_id;
return row;
end;
$BODY$
LANGUAGE 'plpgsql' VOLATILE;

CREATE OR REPLACE FUNCTION jrnx_del()
  RETURNS "trigger" AS
$BODY$
declare
row jrnx%ROWTYPE;
begin
row:=OLD;
insert into del_jrnx select * from jrnx where j_id=row.j_id;
return row;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

CREATE TRIGGER t_jrnx_del
  BEFORE DELETE
  ON jrnx
  FOR EACH ROW
  EXECUTE PROCEDURE jrnx_del();

CREATE TRIGGER t_jrn_del
  BEFORE DELETE
  ON jrn
  FOR EACH ROW
  EXECUTE PROCEDURE jrn_del();

update version set val=42;
commit;