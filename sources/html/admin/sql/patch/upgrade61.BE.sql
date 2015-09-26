
begin;
insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent,pcm_type) values (4515,'Tva Intracomm 0%',451,'PAS');	
insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent,pcm_type) values (4516,'Tva Export 0%',451,'PAS');
insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent,pcm_type) values (4115,'Tva Intracomm 0%',411,'ACT');	
insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent,pcm_type) values (4116,'Tva Export 0%',411,'ACT');
insert into tva_rate (tva_id,tva_label,tva_rate,tva_comment,tva_poste) values (5,'INTRA',0,'Tva pour les livraisons / acquisition intra communautaires','4115,4515');
insert into tva_rate (tva_id,tva_label,tva_rate,tva_comment,tva_poste) values (6,'EXPORT',0,'Tva pour les exportations','4116,4516');

update version set val=62;
commit;


