begin;
alter table jrnx add f_id bigint;
alter table jrnx alter f_id set default null;
update  jrnx set f_id=(select f_id from fiche join jnt_fic_att_value using(f_id) join attr_value using(jft_id)  where ad_id=23 and av_text=jrnx.j_qcode);
alter table quant_sold alter qs_internal drop not null;
alter table quant_purchase alter qp_internal drop not null;
alter table attr_Def add ad_size text;
update attr_def set ad_size='8' where ad_type='date';
update attr_def set ad_size='6' where ad_type='numeric';
update attr_def set ad_size='22' where ad_size is null;
alter table attr_def alter ad_size set not null;

ALTER TABLE jrnx  ADD CONSTRAINT jrnx_f_id_fkey FOREIGN KEY (f_id)      REFERENCES fiche (f_id) MATCH SIMPLE      ON UPDATE cascade ON DELETE NO ACTION;
CREATE INDEX fki_jrnx_f_id  ON jrnx  USING btree  (f_id);

CREATE OR REPLACE FUNCTION correct_quant_purchase() returns void
as
$BODY$
declare
	r_invalid quant_purchase;
	s_QuickCode text;
	b_j_debit bool;
	r_new record;
	r_jrnx record;
begin

for r_invalid in select * from quant_purchase where qp_valid='A'
loop

-- get qcode 
select j_qcode into s_QuickCode from vw_poste_qcode where f_id=r_invalid.qp_fiche;
raise notice 'qp_id % Quick code is %',r_invalid.qp_id,s_QuickCode;

-- get deb or cred
select j_debit,j_grpt,j_jrn_def,j_montant into r_jrnx from jrnx where j_id=r_invalid.j_id;
if NOT FOUND then
	raise notice 'error not found jrnx %',r_invalid.j_id;
	update quant_purchase set qp_valid='Y' where qp_id=r_invalid.qp_id;
	continue;
end if;
raise notice 'j_debit % , j_grpt % ,j_jrn_def  % qp_price %',r_jrnx.j_debit,r_jrnx.j_grpt,r_jrnx.j_jrn_def ,r_invalid.qp_price;

select jr_internal,j_id,j_montant into r_new
	from jrnx join jrn on (j_grpt=jr_grpt_id)
	where 
	j_jrn_def=r_jrnx.j_jrn_def
	and j_id not in (select j_id from  quant_purchase)
	and j_qcode=s_QuickCode
	and j_montant=r_jrnx.j_montant
	and j_debit != r_jrnx.j_debit;

if NOT FOUND then
	raise notice 'error not found %', r_invalid.j_id;
	update quant_purchase set qp_valid='Y' where qp_id=r_invalid.qp_id;
	continue;     
end if;
raise notice 'j_id % found amount %',r_new.j_id,r_new.j_montant;

-- insert into quant_purchase
insert into quant_purchase (qp_internal,j_id,qp_fiche,qp_quantite,qp_price,qp_vat,qp_nd_amount,qp_nd_tva_recup,qp_valid,qp_dep_priv,qp_supplier,qp_vat_code)
values (r_new.jr_internal,r_invalid.j_id,r_invalid.qp_fiche,(r_invalid.qp_quantite * (-1)),r_invalid.qp_price * (-1),r_invalid.qp_vat*(-1),r_invalid.qp_nd_amount*(-1),r_invalid.qp_nd_tva_recup*(-1) ,'Y',r_invalid.qp_dep_priv*(-1),r_invalid.qp_supplier,r_invalid.qp_vat_code);

update quant_purchase set qp_valid='Y' where qp_id=r_invalid.qp_id;
end loop;
return;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

select correct_quant_purchase();

CREATE OR REPLACE FUNCTION correct_quant_sale() returns void
as
$BODY$
declare
	r_invalid quant_sold;
	s_QuickCode text;
	b_j_debit bool;
	r_new record;
	r_jrnx record;
begin

for r_invalid in select * from quant_sold where qs_valid='A'
loop

-- get qcode 
select j_qcode into s_QuickCode from vw_poste_qcode where f_id=r_invalid.qs_fiche;
raise notice 'qp_id % Quick code is %',r_invalid.qs_id,s_QuickCode;

-- get deb or cred
select j_debit,j_grpt,j_jrn_def,j_montant into r_jrnx from jrnx where j_id=r_invalid.j_id;
if NOT FOUND then
	update quant_sold set qs_valid='Y' where qs_id=r_invalid.qs_id;
	raise notice 'error not found jrnx %',r_invalid.j_id;
	continue;
end if;
raise notice 'j_debit % , j_grpt % ,j_jrn_def  % qs_price %',r_jrnx.j_debit,r_jrnx.j_grpt,r_jrnx.j_jrn_def ,r_invalid.qs_price;

select jr_internal,j_id,j_montant into r_new
	from jrnx join jrn on (j_grpt=jr_grpt_id)
	where 
	j_jrn_def=r_jrnx.j_jrn_def
	and j_id not in (select j_id from  quant_sold)
	and j_qcode=s_QuickCode
	and j_montant=r_jrnx.j_montant
	and j_debit != r_jrnx.j_debit;

if NOT FOUND then
   update quant_sold set qs_valid='Y' where qs_id=r_invalid.qs_id;
	raise notice 'error not found %', r_invalid.j_id;
	continue;
end if;
raise notice 'j_id % found amount %',r_new.j_id,r_new.j_montant;

-- insert into quant_sold

 insert into quant_sold (qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_valid,qs_client,qs_vat_code)
 values (r_new.jr_internal,r_invalid.j_id,r_invalid.qs_fiche,(r_invalid.qs_quantite * (-1)),r_invalid.qs_price * (-1),r_invalid.qs_vat*(-1),'Y',r_invalid.qs_client,r_invalid.qs_vat_code);
 update quant_sold set qs_valid='Y' where qs_id=r_invalid.qs_id;
end loop;
return;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

select correct_quant_sale() ;

ALTER TABLE jrn_def ADD COLUMN jrn_def_bank bigint;
alter table jrn_def add jrn_def_num_op integer;
ALTER TABLE del_jrnx ADD COLUMN f_id bigint;


CREATE OR REPLACE FUNCTION comptaproc.jrnx_del()
  RETURNS trigger AS
$BODY$
declare
row jrnx%ROWTYPE;
begin
row:=OLD;


insert into del_jrnx(
            j_id, j_date, j_montant, j_poste, j_grpt, j_rapt, j_jrn_def, 
            j_debit, j_text, j_centralized, j_internal, j_tech_user, j_tech_date, 
            j_tech_per, j_qcode, f_id)  SELECT j_id, j_date, j_montant, j_poste, j_grpt, j_rapt, j_jrn_def, 
       j_debit, j_text, j_centralized, j_internal, j_tech_user, j_tech_date, 
       j_tech_per, j_qcode, f_id from jrnx where j_id=row.j_id;
return row;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

ALTER TABLE stock_goods   ALTER COLUMN f_id DROP NOT NULL;

CREATE OR REPLACE FUNCTION comptaproc.jnt_fic_attr_ins()
  RETURNS trigger AS
$BODY$
declare
   r_record jnt_fic_attr%ROWTYPE;
   i_max integer;
begin
r_record=NEW;
perform comptaproc.fiche_attribut_synchro(r_record.fd_id);
select coalesce(max(jnt_order),0) into i_max from jnt_fic_attr where fd_id=r_record.fd_id;
i_max := i_max + 10;
NEW.jnt_order=i_max;
return NEW;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

CREATE TRIGGER t_jnt_fic_attr_ins
  BEFORE INSERT
  ON jnt_fic_attr
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.jnt_fic_attr_ins();


update version set val=87;

commit;
