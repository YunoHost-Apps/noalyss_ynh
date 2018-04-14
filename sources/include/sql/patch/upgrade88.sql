begin;

update quant_sold set qs_price=abs(qs_price)*(-1), qs_vat=abs(qs_vat)*(-1), qs_quantite=abs(qs_quantite)*(-1) where qs_price < 0 or qs_quantite < 0 or qs_vat < 0;

update quant_purchase set qp_price=abs(qp_price)*(-1), qp_vat=abs(qp_vat)*(-1), qp_quantite=abs(qp_quantite)*(-1) where qp_price < 0 or qp_quantite < 0 or qp_vat < 0;


select comptaproc.fill_quant_fin();

create function comptaproc.quant_purchase_ins_upd () returns trigger
as
$$
	begin
		if NEW.qp_price < 0 OR NEW.qp_quantite <0 THEN
			NEW.qp_price := abs (NEW.qp_price)*(-1);
			NEW.qp_quantite := abs (NEW.qp_quantite)*(-1);
		end if;
return NEW;
end;
$$ 
language plpgsql;

drop trigger if exists quant_purchase_ins_upd_tr on quant_purchase ;
create trigger quant_sold_ins_upd_tr after insert or update on quant_purchase for each row execute procedure comptaproc.quant_purchase_ins_upd();

create function comptaproc.quant_sold_ins_upd () returns trigger
as
$$
	begin
		if NEW.qs_price < 0 OR NEW.qs_quantite <0 THEN
			NEW.qs_price := abs (NEW.qs_price)*(-1);
			NEW.qs_quantite := abs (NEW.qs_quantite)*(-1);
		end if;
return NEW;
end;
$$ 
language plpgsql;

drop trigger if exists quant_sold_ins_upd_tr on quant_sold ;
create trigger quant_sold_ins_upd_tr after insert or update on quant_sold for each row execute procedure comptaproc.quant_sold_ins_upd();

update version set val=89;

commit;