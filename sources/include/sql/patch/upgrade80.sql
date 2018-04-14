begin;
INSERT INTO format_csv_banque (name, include_file) VALUES ('CBC Online', 'cbc_be_ol.inc.php');
update version set val=81;
commit;