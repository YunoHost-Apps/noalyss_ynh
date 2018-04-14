begin;
create view v_tva_rate as select 
	tva_id,
	tva_rate,
	tva_label,
	tva_comment,
	split_part(tva_poste,',',1) as tva_purchase,
	split_part(tva_poste,',',2) as tva_sale,
	tva_both_side 
from tva_rate;

comment on view v_tva_rate is 'Show this table to be easily used by  Tva_Rate_MTable';
comment on column v_tva_rate.tva_purchase is ' VAT used for purchase';
comment on column v_tva_rate.tva_sale  is ' VAT used for sale';
comment on column v_tva_rate.tva_both_side  is 'if 1 ,  VAT avoided ';

insert into version (val,v_description) values (128,'Add a view to manage VAT');
commit;