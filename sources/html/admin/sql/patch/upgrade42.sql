begin;

delete from tva_rate where tva_id in (select tva_id from tva_rate group by tva_id having count(tva_id) > 1);
 update quant_sold set qs_vat_code = null where qs_vat_Code not in (select tva_id from tva_rate);
 update quant_purchase set qp_vat_code = null where qp_vat_Code not in (select tva_id from tva_rate);

alter table tva_rate add constraint tva_id_pk primary key (tva_id);
alter table quant_purchase add constraint qp_vat_code_fk foreign key(qp_vat_code) references tva_rate(tva_id);
alter table quant_sold add constraint qs_vat_code_fk foreign key(qs_vat_code) references tva_rate(tva_id);

update version set val=43;
commit;
