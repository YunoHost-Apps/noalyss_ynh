begin;

update attr_def set ad_type='poste' where ad_id=5;
update attr_def set ad_type='numeric',ad_size=6 where ad_id in (20,31);
update attr_def set ad_size=17 where ad_type='poste';

insert into tmp_pcmn (pcm_val,pcm_lib,pcm_val_parent,pcm_type) select split_part(tva_poste,',',1),tva_comment,substring(split_part(tva_poste,',',1),1,3),'PAS'  from tva_rate where split_part(tva_poste,',',1) not in (select pcm_val from tmp_pcmn);
insert into tmp_pcmn (pcm_val,pcm_lib,pcm_val_parent,pcm_type) select split_part(tva_poste,',',2),tva_comment,substring(split_part(tva_poste,',',2),1,3),'ACT'  from tva_rate where split_part(tva_poste,',',2) not in (select pcm_val from tmp_pcmn);


update version set val=99;

commit;