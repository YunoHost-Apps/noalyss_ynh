begin;
create or replace function fiche_attribut_synchro (p_fd_id fiche_def.fd_id%TYPE) returns void as
$BODY$
declare
	-- this sql gives the f_id and the missing attribute (ad_id)
	list_missing cursor for select f_id,fd_id,ad_id,jnt_order from jnt_fic_attr join fiche as A using (fd_id) where fd_id=p_fd_id and ad_id not in (select ad_id from fiche join jnt_fic_att_value using (f_id) where fd_id=jnt_fic_attr.fd_id and A.f_id=f_id);
	rec record;
	-- value of the last insert
	jnt jnt_fic_att_value%ROWTYPE;
begin
	open list_missing;
	loop
	
	fetch list_missing into rec;
	IF NOT FOUND then
		exit;
	end if;
	-- insert a value into jnt_fic_att_value
	insert 	into jnt_fic_att_value (f_id,ad_id) values (rec.f_id,rec.ad_id) returning * into jnt;

	-- now we insert into attr_value
	insert into attr_value values (jnt.jft_id,'');
	end loop;
	close list_missing;
end; 
$BODY$ language plpgsql;

create or replace function attribute_correct_order () returns void as
$BODY$
declare
    crs_correct cursor for select A.jnt_id,A.jnt_order from jnt_fic_attr as A join jnt_fic_attr as B using (fd_id) where A.jnt_order=B.jnt_order and A.jnt_id > B.jnt_id;
    rec record;
begin
	open crs_correct;
	loop
	fetch crs_correct into rec;
	if NOT FOUND then
		close crs_correct;
		return;
	end if;
	update jnt_fic_attr set jnt_order=jnt_order + 1 where jnt_id = rec.jnt_id;
	end loop;
	close crs_correct;
	perform attribute_correct_order ();
end;
$BODY$ language plpgsql;

select fiche_attribut_synchro(fd_id) from fiche_def;
select attribute_correct_order();
update version set val=55;

commit;