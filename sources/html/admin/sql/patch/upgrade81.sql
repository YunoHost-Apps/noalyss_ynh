begin;

ALTER TABLE import_tmp ADD COLUMN it_pj text;

update version set val=82;
commit;
