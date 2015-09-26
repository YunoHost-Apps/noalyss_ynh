begin ;
alter table jrn_action drop constraint "$1";
alter table jrn_def drop constraint "$1";

update jrn_action set ja_jrn_type='ODS' where ja_jrn_type='OD ';
update jrn_def set jrn_def_type='ODS' where jrn_def_type = 'OD ';
update jrn_type set jrn_type_id='ODS' where jrn_type_id ='OD ';

alter table jrn_action add constraint "$1" foreign key (ja_jrn_type) references   jrn_type(jrn_type_id);

alter table jrn_def add constraint "$1" FOREIGN KEY (jrn_def_type) REFERENCES jrn_type(jrn_type_id);
update version set val=31;

commit;
