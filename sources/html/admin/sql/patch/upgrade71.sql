begin;
alter table document_modele add md_affect varchar(3) default null;
update document_modele set md_affect='ACH' where md_type=10;
update document_modele set md_affect='VEN' where md_type=4;
update document_modele set md_affect='GES' where md_affect is null;
alter table document_modele alter md_affect set not null;
update version set val=72;
commit;
