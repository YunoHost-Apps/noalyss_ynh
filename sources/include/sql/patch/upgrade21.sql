begin;
alter table import_tmp add jr_rapt text;

delete from jnt_fic_attr 
	where jnt_id in (
	select a.jnt_id 
	from jnt_fic_attr as a join jnt_fic_attr as b 
		on (a.fd_id=b.fd_id and a.ad_id=b.ad_id) 
	where b.jnt_id > a.jnt_id);

create unique index fd_id_ad_id_x on jnt_fic_attr( fd_id,ad_id);

update version set val=22;
commit;
