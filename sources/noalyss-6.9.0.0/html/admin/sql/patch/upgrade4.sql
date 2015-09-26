-- upgrade
comment on table action is 'The different privileges';
comment on table attr_def is 'The available attributs for the cards';
comment on table attr_min is 'The minimum attributs for the cards';
comment on table attr_min is 'The value of  attributs for the cards';
comment on table centralized is 'The centralized journal';
comment on table fiche is 'Cards';
comment on table fiche_def is 'Cards definition';
comment on table fiche_def_ref is 'Family Cards definition';
comment on table form is 'Forms';
comment on table form is 'Forms content';
comment on table jnt_fic_att_value is 'join between the card and the attribut definition';
comment on table jnt_fic_attr is 'join between the family card and the attribut definition';
comment on table jrn is 'Journal: content one line for a group of accountancy writing';
comment on table jrnx is 'Journal: content one line for each accountancy writing';
comment on table jrn_action is 'Possible action when we are in journal (menu)';
comment on table jrn_def is 'Definition of a journal, his properties';
comment on table jrn_rapt is 'Rapprochement between operation';
comment on table jrn_type is 'Type of journal (Sell, Buy, Financial...)';
comment on table parm_money is 'Currency conversion';
comment on table parm_periode is 'Periode definition';
comment on table stock_goods is 'About the goods';
comment on table tmp_pcmn is 'Plan comptable minimum normalisé';
comment on table tva_rate is 'Rate of vat';
create sequence s_central;

-- create index x_jr_grpt_id on jrn (jr_grpt_id);
-- create index x_j_grpt on jrnx(j_grpt);
create index x_poste on jrnx(j_poste );
delete from jrn_action where ja_name='Impression' or ja_name = 'Recherche';
delete from fiche where  f_id not in (select f_id from jnt_fic_att_value);
alter table jrn add jr_opid int4;
alter table jrn add  jr_c_opid int4;
create SEQUENCE s_central_order;
alter table centralized add c_order int4;


-- decentralize
delete from centralized;
create sequence s_internal;
select setval('s_centralized',1,false);
update jrnx set j_centralized='f';
alter table parm_periode add p_central bool;
alter table parm_periode alter p_central set default false;
update parm_periode set p_central ='f';
--for uploading doc
alter table jrn add jr_pj oid ;
alter table jrn add  jr_pj_name text;
alter table jrn add  jr_pj_type text;

-- task 3858
delete from user_sec_act WHERE ua_act_id =14;
delete from action where ac_id=14;
insert into action values (18,'Devise');
insert into action values (19,'Période');
insert into action values (20,'Voir la balance des comptes');

-- task 3374
insert into jrn_action (ja_id,ja_name,ja_desc,ja_url,ja_action,ja_jrn_type) 
values (40,'Soldes','Voir les soldes des comptes en banques',
'user_jrn.php','action=solde','FIN');

-- always last line
update version set val=5;
