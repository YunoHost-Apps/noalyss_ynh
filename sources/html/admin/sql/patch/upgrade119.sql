begin;
update menu_ref set me_file = null where me_code='EXT';
update version set val=120;

commit;