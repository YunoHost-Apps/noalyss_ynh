begin;
--make sure that p_start < p_end 
ALTER TABLE parm_periode ADD CHECK (p_end >= p_start);
insert into tva_rate values (5,'0%',0, 'Pas soumis à la TVA',null);


update  version set val=7;
commit;