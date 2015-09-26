begin;
delete from letter_deb where ld_id in (select a.ld_id from letter_deb as a join letter_deb as b on (a.j_id=b.j_id  and a.ld_id > b.ld_id));
delete from letter_cred where lc_id in (select a.lc_id from letter_cred as a join letter_cred as b on (a.j_id=b.j_id  and a.lc_id > b.lc_id));

ALTER TABLE letter_deb  ADD CONSTRAINT letter_deb_j_id_key UNIQUE(j_id );
ALTER TABLE letter_cred  ADD CONSTRAINT letter_cred_j_id_key UNIQUE(j_id );


update version set val=102;

commit;