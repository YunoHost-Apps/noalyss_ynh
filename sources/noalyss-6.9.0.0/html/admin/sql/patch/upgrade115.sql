begin;

INSERT INTO menu_ref(me_code, me_menu, me_file,   me_type)VALUES ('PDF:AncReceipt', 'Export pièce PDF',  'export_anc_receipt_pdf.php','PR');

insert into profile_menu(me_code,p_id,p_type_display,pm_default) values ('PDF:AncReceipt',1,'P',0);

update version set val=116;

commit;
