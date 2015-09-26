begin;

drop function if exists comptaproc.get_pcm_tree(account_type);

create or replace function comptaproc.get_pcm_tree(source account_type) returns setof account_type
as
$_$
declare
	i account_type;
	e account_type;
begin
	for i in select pcm_val from tmp_pcmn where pcm_val_parent=source
	loop
		return next i;
		for e in select get_pcm_tree from get_pcm_tree(i)
		loop
			return next e;
		end loop;

	end loop;
	return;
end;
$_$
language plpgsql;

drop table if exists letter_deb;
drop table if exists letter_cred;
drop table if exists jnt_letter cascade;
create table jnt_letter(
	jl_id serial not null,
	jl_amount_deb numeric(20,4),
	constraint jnt_letter_pk primary key (jl_id)
	);
create table letter_deb (
	ld_id serial,
	j_id bigint not null,
	jl_id bigint not null,
	constraint letter_deb_pk primary key (ld_id),
	constraint letter_deb_fk foreign key (j_id) references jrnx(j_id) on update cascade on delete cascade,
	constraint jnt_deb_fk foreign key (jl_id) references jnt_letter(jl_id) on update cascade on delete cascade
	);

create table letter_cred (
	lc_id serial,
	j_id bigint not null,
	jl_id bigint not null,
	constraint letter_cred_pk primary key (lc_id),
	constraint letter_cred_fk foreign key (j_id) references jrnx(j_id) on update cascade on delete cascade,
	constraint jnt_cred_fk foreign key (jl_id) references jnt_letter(jl_id) on update cascade on delete cascade
	);


create or replace function comptaproc.get_letter_jnt(a bigint) returns bigint
as
$_$
declare
 nResult bigint;
begin
   select jl_id into nResult from jnt_letter join letter_deb using (jl_id) where j_id = a;
   if NOT FOUND then
	select jl_id into nResult from jnt_letter join letter_cred using (jl_id) where j_id = a;
	if NOT found then
		return null;
	end if;
    end if;
return nResult;
end;
$_$ language plpgsql;

update version set val=75;
commit;
