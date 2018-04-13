begin;
-- add style
insert into theme (the_name,the_filestyle) values ('Classic7','style-classic7.css');
delete from theme where the_filestyle in ('style-mandarine.css','style-mobile.css');
update user_global_pref set parameter_value='Classic7'  where parameter_type='THEME';
-- add constraint
alter table jnt_use_dos add CONSTRAINT use_id_dos_id_uniq UNIQUE (use_id,dos_id);
-- create table to check progress
create table progress 
(
    p_id varchar(16) primary key,
    p_value numeric (5,2) not null ,
    p_created timestamp default now()
);
select upgrade_repo(18);
commit;