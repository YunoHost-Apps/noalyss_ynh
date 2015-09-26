begin;

insert into format_csv_banque values ('Dexia','dexia_be.inc.php');
alter table stock_goods alter sg_quantity type numeric(8,4);
alter table stock_goods add sg_comment varchar(80);
alter table stock_goods add sg_exercice varchar(4);
update version set val=28;
commit;