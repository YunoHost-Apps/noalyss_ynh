begin;

alter table operation_analytique rename oa_signed to oa_positive;

update version set val=114;

commit;
