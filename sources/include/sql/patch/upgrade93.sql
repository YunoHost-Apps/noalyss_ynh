begin;

alter table operation_analytique drop column pa_id;
ALTER TABLE operation_analytique  ADD CONSTRAINT operation_analytique_oa_amount_check CHECK (oa_amount >= 0::numeric);

create type anc_table_card_type as (po_id bigint,pa_id bigint,PO_NAME TEXT,po_description text,sum_amount numeric(25,4),f_id bigint,card_account text,name text);

create or replace function comptaproc.table_analytic_card (p_from text,p_to text)
returns setof anc_table_card_type  
as 
$BODY$
declare
	ret ANC_table_card_type%ROWTYPE;
	sql_from text:='';
	sql_to text:='';
	sWhere text:='';
	sAnd text:='';
	sResult text:='';
begin
if p_from <> '' and p_from is not null then
	sql_from:='oa_date >= to_date('''||p_from::text||''',''DD.MM.YYYY'')';
	sWhere:=' where ';
end if;

if p_to <> '' and p_to is not null then
	sql_to=' oa_date <= to_date('''||p_to::text||''',''DD.MM.YYYY'')';
	sWhere := ' where ';
end if;

if sql_to <> '' and sql_from <> '' then
	sAnd :=' and ';
end if;

sResult := sWhere || sql_from || sAnd || sql_to;

for ret in EXECUTE ' SELECT po.po_id, po.pa_id, po.po_name, po.po_description,  sum(
        CASE
            WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
            ELSE operation_analytique.oa_amount
        END) AS sum_amount, jrnx.f_id, jrnx.j_qcode, ( SELECT fiche_detail.ad_value
           FROM fiche_detail
          WHERE fiche_detail.ad_id = 1 AND fiche_detail.f_id = jrnx.f_id) AS name
   FROM operation_analytique
   JOIN poste_analytique po USING (po_id)
   JOIN jrnx USING (j_id)'|| sResult ||'
  GROUP BY po.po_id, po.po_name, po.pa_id, jrnx.f_id, jrnx.j_qcode, ( SELECT fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 1 AND fiche_detail.f_id = jrnx.f_id), po.po_description
 HAVING sum(
CASE
    WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
    ELSE operation_analytique.oa_amount
END) <> 0::numeric;'


	loop
	return next ret;
end loop;
end;
$BODY$ language plpgsql;


create type anc_table_account_type as (po_id bigint,pa_id bigint,PO_NAME TEXT,po_description text,sum_amount numeric(25,4),card_account text,name text);


create or replace function comptaproc.table_analytic_account (p_from text,p_to text)
returns setof anc_table_account_type 
as 
$BODY$
declare
	ret ANC_table_account_type%ROWTYPE;
	sql_from text:='';
	sql_to text:='';
	sWhere text:='';
	sAnd text:='';
	sResult text:='';
begin
if p_from <> '' and p_from is not null then
	sql_from:='oa_date >= to_date('''||p_from::text||''',''DD.MM.YYYY'')';
	sWhere:=' where ';
end if;

if p_to <> '' and p_to is not null then
	sql_to=' oa_date <= to_date('''||p_to::text||''',''DD.MM.YYYY'')';
	sWhere := ' where ';
end if;

if sql_to <> '' and sql_from <> '' then
	sAnd:=' and ';
end if;

sResult := sWhere || sql_from || sAnd || sql_to;

for ret in EXECUTE 'SELECT po.po_id,
			    po.pa_id, po.po_name, 
			    po.po_description,sum(
        CASE
            WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
            ELSE operation_analytique.oa_amount
        END) AS sum_amount, jrnx.j_poste, tmp_pcmn.pcm_lib AS name
   FROM operation_analytique
   JOIN poste_analytique po USING (po_id)
   JOIN jrnx USING (j_id)
   JOIN tmp_pcmn ON jrnx.j_poste::text = tmp_pcmn.pcm_val::text
'|| sResult ||'
  GROUP BY po.po_id, po.po_name, po.pa_id, jrnx.j_poste, tmp_pcmn.pcm_lib, po.po_description
 HAVING sum(
CASE
    WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
    ELSE operation_analytique.oa_amount
END) <> 0::numeric '
	loop
	return next ret;
end loop;
end;
$BODY$ language plpgsql;

update operation_analytique set oa_date=j_date
       from jrnx
       where operation_analytique.j_id=jrnx.j_id
       and operation_analytique.j_id in (select j_id
						from jrnx join jrn on (j_grpt=jr_grpt_id)
					);

update version set val=94;
commit;
