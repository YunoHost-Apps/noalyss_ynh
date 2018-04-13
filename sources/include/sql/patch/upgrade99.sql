begin;
CREATE OR REPLACE FUNCTION comptaproc.get_menu_dependency(profile_menu_id int)
  RETURNS SETOF int AS
$BODY$
declare
	i int;
	x int;
	e int;
begin
	for x in select pm_id,me_code
			from profile_menu
			where me_code_dep in (select me_code from profile_menu where pm_id=profile_menu_id)
			and p_id = (select p_id from profile_menu where pm_id=profile_menu_id)
	loop
		return next x;

	for e in select *  from comptaproc.get_menu_dependency(x)
		loop
			return next e;
		end loop;

	end loop;
	return;
end;
$BODY$
LANGUAGE plpgsql;

delete from profile_menu where p_id=2 and me_code_dep='DIVPARM';
delete from profile_menu where p_id=2 and me_code_dep='MOD';

update quant_sold set qs_price=(-1)*qs_price, qs_vat=(-1)*qs_vat where qs_quantite < 0 and qs_price > 0 and qs_vat >= 0;

update quant_purchase  set qp_price=(-1)*qp_price, qp_vat=(-1)*qp_vat,
 qp_nd_amount=(-1)*qp_nd_amount,
 qp_nd_tva=(-1)*qp_nd_tva,
 qp_nd_tva_recup=(-1)*qp_nd_tva_recup,
 qp_dep_priv=(-1)*qp_dep_priv
where qp_quantite < 0 and qp_price > 0 and qp_vat >= 0;

update jrnx set j_text = null from jrn where jr_grpt_id=j_grpt and j_text=jr_comment;
insert into parameter (pr_id,pr_value) values ('MY_UPDLAB','N');

update version set val=100;

commit;