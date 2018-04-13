begin;

ALTER TABLE quant_sold  ADD CONSTRAINT qs_id_pk PRIMARY KEY(qs_id);
COMMENT ON TABLE quant_sold IS 'Contains about invoice for customer';
drop table user_pref;
-- trim the space
update parm_code set p_code=trim(p_code);

update version set val=13;

commit;
