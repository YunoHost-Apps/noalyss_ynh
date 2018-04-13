begin;

insert into parm_code values ('DEP_PRIV',4890,'Depense a charge du gerant');
insert into attr_def values (31,'Depense à  charge du gérant (partie privée)');
alter table quant_purchase add qp_dep_priv numeric(20,4) default 0.0;

CREATE FUNCTION insert_quant_purchase(p_internal text, p_j_id numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_nd_amount numeric, p_nd_tva numeric, p_nd_tva_recup numeric,p_dep_priv numeric , p_client character varying) RETURNS void
    AS $$
declare
        fid_client integer;
        fid_good   integer;
begin
        select f_id into fid_client from
                attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(p_client);
        select f_id into fid_good from
                attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(p_fiche);
        insert into quant_purchase
                (qp_internal,
		j_id,
		qp_fiche,
		qp_quantite,
		qp_price,
		qp_vat,
		qp_vat_code,
		qp_nd_amount,
		qp_nd_tva,
		qp_nd_tva_recup,
		qp_supplier,
		qp_dep_priv)
        values
                (p_internal,
		p_j_id,
		fid_good,	
		p_quant,
		p_price,
		p_vat,
		p_vat_code,
		p_nd_amount,
		p_nd_tva,
		p_nd_tva_recup,
		fid_client,
		p_dep_priv);
        return;
end;
 $$
    LANGUAGE plpgsql;

update version set val=41;
commit;
