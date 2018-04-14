begin;
CREATE OR REPLACE FUNCTION comptaproc.anc_correct_tvand()
RETURNS VOID
AS $function$ 
declare
        n_count numeric;
        i record;
        newrow_tva record;
begin
         for i in select * from operation_analytique where oa_jrnx_id_source is not null loop
         -- Get all the anc accounting from the base operation and insert the missing record for VAT 
                for newrow_tva in select *  from operation_analytique where j_id=i.oa_jrnx_id_source and po_id <> i.po_id loop
                    
                        -- check if the record is yet present
                        select count(*) into n_count from operation_analytique where  po_id=newrow_tva.po_id and oa_jrnx_id_source=i.oa_jrnx_id_source;

                        if n_count = 0 then
                          raise info 'insert operation analytique po_id = % oa_group = % ',i.po_id, i.oa_group;
                          insert into operation_analytique 
                          (po_id,oa_amount,oa_description,oa_debit,j_id,oa_group,oa_date,oa_jrnx_id_source,oa_positive)
                          values (newrow_tva.po_id,i.oa_amount,i.oa_description,i.oa_debit,i.j_id,i.oa_group,i.oa_date,i.oa_jrnx_id_source,i.oa_positive);
                        end if;
         
                end loop;

         
         end loop;
end;
 $function$
LANGUAGE plpgsql;

select comptaproc.anc_correct_tvand();

update version set val=126;

commit;