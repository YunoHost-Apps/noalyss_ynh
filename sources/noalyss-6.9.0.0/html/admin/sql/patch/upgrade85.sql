begin;

CREATE OR REPLACE FUNCTION comptaproc.jrnx_ins()
  RETURNS trigger AS
$BODY$
declare
n_fid bigint;
begin

if NEW.j_qcode is NULL then
   return NEW;
end if;

NEW.j_qcode=trim(upper(NEW.j_qcode));

if length (NEW.j_qcode) = 0 then
    NEW.j_qcode=NULL;
    else
   select f_id into n_fid from fiche join jnt_fic_att_value using (f_id) join attr_value using(jft_id) where ad_id=23 and av_text=NEW.j_qcode;
        if NOT FOUND then 
                raise exception 'La fiche dont le quick code est % n''existe pas',NEW.j_qcode;
        end if;
end if;
return NEW;
end;
$BODY$
  LANGUAGE 'plpgsql' ;
-- update jrn set jr_internal=jrn.jr_internal||jrn.jr_id::text from jrn as B where jrn.jr_internal=B.jr_internal and jrn.jr_id > B.jr_id;
-- create unique index ux_jr_internal on jrn(jr_internal);

delete from quant_purchase where qp_internal not in (select jr_internal from jrn);
alter table quant_purchase  ADD CONSTRAINT quant_purchase_qp_internal_fkey FOREIGN KEY (qp_internal)
      REFERENCES jrn (jr_internal) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

delete from quant_sold where qs_internal not in (select jr_internal from jrn);

ALTER TABLE quant_sold
  ADD CONSTRAINT quant_sold_qs_internal_fkey FOREIGN KEY (qs_internal)
      REFERENCES jrn (jr_internal) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

delete  from stock_goods where j_id not in (select j_id from jrnx);

delete from stock_goods where j_id not in (select j_id from jrnx);

ALTER TABLE stock_goods
  ADD CONSTRAINT stock_goods_j_id_fkey FOREIGN KEY (j_id)
      REFERENCES jrnx (j_id) MATCH SIMPLE
      ON UPDATE cascade ON DELETE cascade;

delete from jrn_rapt where jr_id not in (select jr_id from jrn);
delete from jrn_rapt where jra_id not in (select jr_id from jrn);

delete from jrn_rapt where jr_id not in (select jr_id from jrn);
delete from jrn_rapt where jra_concerned not in (select jr_id from jrn);

ALTER TABLE jrn_rapt
  ADD CONSTRAINT jrn_rapt_jr_id_fkey FOREIGN KEY (jr_id)
      REFERENCES jrn (jr_id) MATCH SIMPLE
      ON UPDATE cascade ON DELETE cascade;
ALTER TABLE jrn_rapt
  ADD CONSTRAINT jrn_rapt_jra_concerned_fkey FOREIGN KEY (jra_concerned)
      REFERENCES jrn (jr_id) MATCH SIMPLE    
      ON UPDATE cascade ON DELETE cascade;

ALTER TABLE attr_def ADD COLUMN ad_type text;
alter table quant_sold alter qs_internal drop not null;
alter table quant_purchase alter qp_internal drop not null;

update attr_def set ad_type='text';
update attr_def set ad_type='numeric' where ad_id in (6,7,8,11,21,22);
update attr_def set ad_type='date' where ad_id in (10);
alter sequence s_attr_def restart with 9001;
update version set val=86;
commit;
