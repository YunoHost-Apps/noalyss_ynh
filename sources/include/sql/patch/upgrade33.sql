begin ;

alter table op_predef_detail rename od_qc to opd_qc;

update version set val=34;
commit;
