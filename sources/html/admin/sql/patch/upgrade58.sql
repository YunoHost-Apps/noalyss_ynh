begin;
INSERT INTO format_csv_banque(name, include_file)     VALUES('VMS Keytrade', 'keytrade_be.inc.php');

update version set val=59;

commit;

