begin;

set search_path to public,comptaproc;

alter table action_gestion drop ag_ref_ag_id;

drop trigger t_tmp_pcm_alphanum_ins_upd on tmp_pcmn ;
drop     trigger t_tmp_pcmn_ins on tmp_pcmn ;

create sequence tmp_pcmn_id_seq;
ALTER TABLE tmp_pcmn ADD COLUMN id bigint;
update tmp_pcmn set id=nextval('tmp_pcmn_id_seq');

ALTER TABLE tmp_pcmn ALTER COLUMN id SET NOT NULL;
ALTER TABLE tmp_pcmn ALTER COLUMN id SET DEFAULT nextval('tmp_pcmn_id_seq'::regclass);
ALTER TABLE tmp_pcmn   ADD CONSTRAINT id_ux UNIQUE(id);
COMMENT ON COLUMN tmp_pcmn.id IS 'allow to identify the row, it is unique and not null (pseudo pk)';
update tmp_pcmn set id=nextval('tmp_pcmn_id_seq');
alter table tmp_pcmn add column pcm_direct_use varchar(1);
COMMENT ON COLUMN tmp_pcmn.pcm_direct_use IS 'Value are N or Y , N cannot be used directly , not even through a card';
ALTER TABLE tmp_pcmn ALTER COLUMN pcm_direct_use  SET DEFAULT 'Y';
update tmp_pcmn set pcm_direct_use='Y';
update tmp_pcmn set pcm_direct_use='N' where length(pcm_val) < 3 and not exists (select j_poste from jrnx where j_poste=pcm_val);
ALTER TABLE tmp_pcmn ALTER COLUMN pcm_direct_use SET NOT NULL;
alter table tmp_pcmn add constraint pcm_direct_use_ck check (pcm_direct_use in ('Y','N'));

create     trigger t_tmp_pcm_alphanum_ins_upd before insert or update on tmp_pcmn for each row execute procedure comptaproc.tmp_pcmn_alphanum_ins_upd();
create     trigger t_tmp_pcmn_ins before insert on tmp_pcmn for each row execute procedure comptaproc.tmp_pcmn_ins();

select nextval('bilan_b_id_seq');
select nextval('bilan_b_id_seq');
select nextval('bilan_b_id_seq');
select nextval('bilan_b_id_seq');

insert into bilan (b_name,b_file_template,b_file_form,b_type) values ('ASBL','document/fr_be/bnb-asbl.rtf','document/fr_be/bnb-asbl.form','RTF');

alter table jnt_letter drop jl_amount_deb;

ALTER TABLE operation_analytique ADD COLUMN f_id bigint;
ALTER TABLE operation_analytique  ADD CONSTRAINT operation_analytique_fiche_id_fk FOREIGN KEY (f_id)       REFERENCES fiche (f_id) MATCH SIMPLE       ON UPDATE cascade ON delete cascade;
COMMENT ON COLUMN operation_analytique.f_id IS 'FK to fiche.f_id , used only with ODS';

drop FUNCTION comptaproc.table_analytic_account(text,text);
drop FUNCTION comptaproc.table_analytic_card(text,text);

CREATE TABLE public.user_filter (
	id bigserial,
	login text NULL,
	nb_jrn int4 NULL,
	date_start varchar(10) NULL,
	date_end varchar(10) NULL,
	description text NULL,
	amount_min numeric(20,4) NULL,
	amount_max numeric(20,4) NULL,
	qcode text NULL,
	accounting text NULL,
	r_jrn text NULL,
	date_paid_start varchar(10) NULL,
	date_paid_end varchar(10) NULL,
	ledger_type varchar(5) NULL,
	all_ledger int4 NULL,
	filter_name text NOT NULL,
	unpaid varchar NULL,
	PRIMARY KEY (id)
);




alter table jrn_periode drop constraint jrn_periode_pk;
create sequence jrn_periode_id_seq;
alter table jrn_periode add id bigint;
alter table jrn_periode alter column   id set default  nextval('jrn_periode_id_seq');
update jrn_periode set id=nextval('jrn_periode_id_seq');
alter table jrn_periode add  constraint jrn_periode_pk  primary key (id);
alter table jrn_periode add constraint  jrn_periode_periode_ledger unique (jrn_def_id,p_id); 

CREATE TABLE public.user_active_security (
	id serial not NULL,
	us_login text NOT NULL,
	us_ledger varchar(1) not NULL,
	us_action varchar(1) not NULL
);
COMMENT ON COLUMN public.user_active_security.us_login IS 'user''s login' ;
COMMENT ON COLUMN public.user_active_security.us_ledger IS 'Flag Security for ledger' ;
COMMENT ON COLUMN public.user_active_security.us_action IS 'Security for action' ;

ALTER TABLE public.user_active_security ADD CONSTRAINT user_active_security_pk PRIMARY KEY (id) ;
ALTER TABLE public.user_active_security ADD CONSTRAINT user_active_security_ledger_check CHECK (us_ledger in ('Y','N')) ;
ALTER TABLE public.user_active_security ADD CONSTRAINT user_active_security_action_check CHECK (us_action in ('Y','N')) ;

insert into user_active_security (us_login,us_ledger,us_action)  select user_name,'Y','Y' from profile_user;

alter table jrn_def add jrn_enable int;
alter table jrn_def alter  jrn_enable set default 1;
update jrn_def set jrn_enable=1;
comment on column jrn_def.jrn_enable is 'Set to 1 if the ledger is enable ';


alter table jrn add jr_optype varchar(3);
alter table jrn alter jr_optype set default 'NOR';
comment on column jrn.jr_optype is 'Type of operation , NOR = NORMAL , OPE opening , EXT extourne, CLO closing';
update jrn set jr_optype='NOR';

alter table tags add column t_actif char(1);
update tags set t_actif='Y';
ALTER TABLE tags ADD CONSTRAINT tags_check CHECK (t_actif in ('N','Y')) ;
alter table tags alter t_actif set default 'Y';
COMMENT ON COLUMN tags.t_actif is 'Y if the tag is activate and can be used ';
alter table version add v_description text;
alter table version add v_date timestamp;
alter table version alter v_date set default now();
 alter table version add primary key (val);
insert into version (val,v_description) values (127,'Add filter for search, inactive tag or ledger, type of operation, security');

commit;
