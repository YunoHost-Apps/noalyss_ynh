begin;

insert into  attr_def(ad_id,ad_text,ad_type,ad_size) values (50,'Contrepartie pour TVA récup par impot','poste',22);
insert into  attr_def(ad_id,ad_text,ad_type,ad_size) values (51,'Contrepartie pour TVA non Ded.','poste',22);
insert into  attr_def(ad_id,ad_text,ad_type,ad_size) values (52,'Contrepartie pour dépense à charge du gérant','poste',22);
insert into  attr_def(ad_id,ad_text,ad_type,ad_size) values (53,'Contrepartie pour dépense fiscal. non déd.','poste',22);
update jrn_def set jrn_def_code=substr(jrn_def_code,1,1)||substr(jrn_def_code,length(jrn_def_code)-1,length(jrn_def_code));

create unique index qs_j_id on quant_sold(j_id);
create unique index qp_j_id on quant_purchase(j_id);
create unique index qf_jr_id on quant_fin(jr_id);

update version set val=93;
commit;
