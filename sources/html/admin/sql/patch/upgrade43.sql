begin;
delete from bud_detail where bd_id not in (select bd_id from bud_detail_periode);
update version set val=44;
commit;
