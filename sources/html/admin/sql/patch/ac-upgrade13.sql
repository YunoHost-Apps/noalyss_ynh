begin;
-- Check: valid_state
 ALTER TABLE audit_connect DROP CONSTRAINT valid_state;

ALTER TABLE audit_connect ADD CONSTRAINT valid_state CHECK (ac_state = 'FAIL'::text OR ac_state = 'SUCCESS'::text or ac_state='AUDIT');
-- run to the account_repository
insert into theme values ('EPad','style-epad.css',null);
update priv_user set priv_priv='R' where priv_priv='L';
update priv_user set priv_priv='R' where priv_priv='P';
update version set val=14;
commit;
