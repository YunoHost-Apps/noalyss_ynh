begin;
update modeledef set mod_desc='Comptabilité Belge, à adapter' where mod_id=1;
update modeledef set mod_desc='Comptabilité Française, à adapter' where mod_id=2;
update version set val=9;
commit;

