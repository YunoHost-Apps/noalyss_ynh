begin ;
delete from jrn where jr_internal is null;
delete from jrnx where j_grpt not in (select jr_grpt_id from jrn);
alter table op_predef add od_direct bool;
update op_predef set od_direct=false;
alter table op_predef alter od_direct set not null;
alter table op_predef_detail add od_qc bool;
update version set val=33;

commit;
