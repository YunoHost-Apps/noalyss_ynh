begin;
create table info_def (
	id_type text primary key,
	id_description text null
);

comment on table info_def is 'Contains the types of additionnal info we can add to a operation';
create or replace function info_def_ins_upd() returns trigger
AS
$$
declare 
	row_info_def info_def%ROWTYPE;
	str_type text;
begin
row_info_def:=NEW;
str_type:=upper(trim(NEW.id_type));
str_type:=replace(str_type,' ','');
str_type:=replace(str_type,',','');
str_type:=replace(str_type,';','');
if  length(str_type) =0 then
	raise exception 'id_type cannot be null';
end if;
row_info_def.id_type:=str_type;
return row_info_def;
end;
$$ language plpgsql;
create trigger info_def_ins_upd_t before insert or update on info_def for each row execute procedure info_def_ins_upd();

create table jrn_info (
	ji_id serial primary key,
	jr_id integer	not null,
	id_type text not null,	
	ji_value text
);
alter table jrn_info add constraint fk_jrn foreign key (jr_id) references jrn(jr_id) on delete cascade on update cascade;
alter table jrn_info add constraint fk_info_def foreign key (id_type) references info_def(id_type) on delete cascade on update cascade;
insert into info_def values ('BON_COMMANDE','Numero de bon de commande') ;
insert into info_def values ('OTHER','Info diverses');
insert into attr_def values(30,'Numero de client');
update version set val=40;
commit;
