create view v_tva_rate as select 
	tva_id,
	tva_rate,
	tva_label,
	tva_comment,
	split_part(tva_poste,',',1) as tva_purchase,
	split_part(tva_poste,',',2) as tva_sale,
	tva_both_side 
from tva_rate;
