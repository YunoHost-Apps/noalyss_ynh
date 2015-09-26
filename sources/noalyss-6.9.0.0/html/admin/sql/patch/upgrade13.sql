begin;
-- dropped all the views
-- Name: vw_client; Type: VIEW; Schema: public; Owner: phpcompta
DROP VIEW vw_client ;
-- Name: vw_fiche_attr; Type: VIEW; Schema: public; Owner: phpcompta
DROP VIEW vw_fiche_attr ;
-- Name: vw_fiche_def; Type: VIEW; Schema: public; Owner: phpcompta
DROP VIEW vw_fiche_def ;
-- Name: vw_fiche_min; Type: VIEW; Schema: public; Owner: phpcompta
DROP VIEW vw_fiche_min ;
-- Name: vw_poste_qcode; Type: VIEW; Schema: public; Owner: phpcompta
DROP VIEW vw_poste_qcode;

-- Stan's problem : account were not large enough
-- Converted to numeric to avoid integer limit
create domain poste_comptable as numeric(25);
alter table tmp_pcmn alter pcm_val type poste_comptable;
alter table tmp_pcmn alter pcm_val_parent type poste_comptable;
alter table jrnx alter j_poste TYPE poste_comptable ;
alter table centralized alter c_poste TYPE poste_comptable ;
alter table fiche_def alter fd_class_base TYPE poste_comptable ;

-- recreate all the views
CREATE VIEW vw_client AS
SELECT a.f_id, a.av_text AS name, a1.av_text AS quick_code, b.av_text AS tva_num, c.av_text AS poste_comptable, d.av_text AS rue, e.av_text AS code_postal, f.av_text AS pays, g.av_text AS telephone, h.av_text AS email FROM (((((((((SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 1)) a JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 13)) b USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 23)) a1 USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 5)) c USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 14)) d USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 15)) e USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 16)) f USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 17)) g USING (f_id)) LEFT JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 18)) h USING (f_id)) WHERE (a.frd_id = 9);
CREATE VIEW vw_fiche_attr AS
SELECT a.f_id, a.fd_id, a.av_text AS vw_name, b.av_text AS vw_sell, c.av_text AS vw_buy, d.av_text AS tva_code, tva_rate.tva_id, tva_rate.tva_rate, tva_rate.tva_label, e.av_text AS vw_addr, f.av_text AS vw_cp, j.av_text AS quick_code, fiche_def.frd_id FROM (((((((((SELECT fiche.f_id, fiche.fd_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 1)) a LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 6)) b ON ((a.f_id = b.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 7)) c ON ((a.f_id = c.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 2)) d ON ((a.f_id = d.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 14)) e ON ((a.f_id = e.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 15)) f ON ((a.f_id = f.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 23)) j ON ((a.f_id = j.f_id))) LEFT JOIN tva_rate ON ((d.av_text = (tva_rate.tva_id)::text))) JOIN fiche_def USING (fd_id));
CREATE VIEW vw_fiche_def AS
SELECT jnt_fic_attr.fd_id, jnt_fic_attr.ad_id, attr_def.ad_text, attr_value.av_text, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def.frd_id FROM (((((jnt_fic_att_value JOIN attr_value USING (jft_id)) JOIN fiche USING (f_id)) JOIN jnt_fic_attr USING (fd_id)) JOIN attr_def ON ((attr_def.ad_id = jnt_fic_attr.ad_id))) JOIN fiche_def USING (fd_id));
CREATE VIEW vw_fiche_min AS
SELECT attr_min.frd_id, attr_min.ad_id, attr_def.ad_text, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base FROM ((attr_min JOIN attr_def USING (ad_id)) JOIN fiche_def_ref USING (frd_id));
CREATE VIEW vw_poste_qcode AS
SELECT a.f_id, a.av_text AS j_poste, b.av_text AS j_qcode FROM ((SELECT jnt_fic_att_value.f_id, attr_value.av_text FROM (attr_value JOIN jnt_fic_att_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 5)) a JOIN (SELECT jnt_fic_att_value.f_id, attr_value.av_text FROM (attr_value JOIN jnt_fic_att_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 23)) b USING (f_id));

-- comment
COMMENT ON VIEW vw_fiche_def IS 'all the attributs for  card family';
-- Name: VIEW vw_fiche_min; Type: COMMENT; Schema: public; Owner: phpcompta
COMMENT ON VIEW vw_fiche_min IS 'minimum attribut for reference card';

create or replace function account_auto (p_fd_id fiche_def.fd_id%type)
returns bool
as
$$
-- account_auto
-- param fd_id
-- return true if the card generate automatically an account
declare
	l_auto bool;
begin

	select fd_create_account into l_auto from fiche_def where fd_id=p_fd_id;
	if l_auto is null then
		l_auto:=false;
	end if;
	return l_auto;
end;
$$ language plpgsql;

create or replace function account_compute(p_f_id fiche.f_id%type)
returns poste_comptable
as
$body$
-- account_compute
-- param f_id
-- compute the next account
-- return new account
declare
	class_base poste_comptable;
	maxcode int8;
begin
 -- Get the class base
	select fd_class_base into class_base 
	from
		fiche_def join fiche using (fd_id)
	where 
		f_id=p_f_id;
	raise notice 'class base %',class_base;
	select max(pcm_val) into maxcode from tmp_pcmn where pcm_val = class_base;
	if maxcode = class_base then
		maxcode=class_base*1000+1;
	end if;
	raise notice 'Max code %',maxcode;
return maxcode+1;
end;
$body$ language plpgsql;



create or replace function attribut_insert ( p_f_id integer, p_ad_id integer, p_value varchar)
returns void
as
$$
-- attribut_integer
-- parameter : f_id, ad_id, p_value
-- purpose add an attribute to a card
-- it inserts a row into jnt_fic_att_value and attr_value
declare 
	n_jft_id integer;
begin
	select nextval('s_jnt_fic_att_value') into n_jft_id;
	 insert into jnt_fic_att_value (jft_id,f_id,ad_id) values (n_jft_id,p_f_id,p_ad_id);
	 insert into attr_value (jft_id,av_text) values (n_jft_id,p_value);
return;
end;
$$
language plpgsql volatile;



CREATE OR REPLACE FUNCTION account_insert(p_f_id fiche.f_id%type,p_account tmp_pcmn.pcm_val%type)
  RETURNS int4 AS
$BODY$
declare
-- account_insert
-- parameter f_id,p_account label of account
-- purpose : create a new account for a card
-- check if the accound needs to be created automatically
-- if p_account is empty or null
-- into tables attr_value
nParent tmp_pcmn.pcm_val_parent%type;
sName varchar;
nNew tmp_pcmn.pcm_val%type;
bAuto bool;
nFd_id integer;
nCount integer;
begin
	
	-- if p_value empty
	if length(trim(p_account)) != 0 then
	-- does the account exist ?
		select *  into nCount from tmp_pcmn where pcm_val=p_account;
		if nCount !=0  then
			-- retrieve name
			select av_text into sName from 
				attr_value join jnt_fic_att_value using (jft_id)
			where	
			ad_id=1 and f_id=p_f_id;
			-- get parent
			nParent:=account_parent(p_account);
			-- account doesn't exist we need to add id
			insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) 
				values (p_account,sName,nParent);
			-- insert as card's attribute
			perform attribut_insert(p_f_id,5,to_char(nNew,'999999999999'));
	
		end if;		
	else 
		select fd_id into nFd_id from fiche where f_id=p_f_id;
		bAuto:= account_auto(nFd_id);

		if bAuto = true then
			-- create automatically the account
			-- compute the next account
			nNew:=account_compute(p_f_id);
raise debug 'nNew %', nNew;
			-- retrieve name
			select av_text into sName from 
			attr_value join jnt_fic_att_value using (jft_id)
			where
			ad_id=1 and f_id=p_f_id;

			-- get parent
			nParent:=account_parent(nNew);
			-- account doesn't exist we need to add id
			perform account_add  (nNew,sName);
			-- insert as card's attribute
			perform attribut_insert(p_f_id,5,to_char(nNew,'999999999999'));
	
		else 
			 perform attribut_insert(p_f_id,5,null);
		end if;

	end if;
		
return 0;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

create or replace function account_parent(p_account tmp_pcmn.pcm_val%type)
returns 
	-- account_parent
	-- parameter pcm_val%type;
	-- purpose compute the parent account	

	poste_comptable
as
$$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	sParent varchar;
	nCount integer;
begin
	sParent:=to_char(p_account,'9999999999999999');
	sParent:=trim(sParent);
	nParent:=0;
	while nParent = 0 loop
		select count(*) into nCount
		from tmp_pcmn
		where
		pcm_val = to_number(sParent,'9999999999999999');
		if nCount != 0 then
			nParent:=to_number(sParent,'9999999999999999');
		end if;
		sParent:= substr(sParent,1,length(sParent)-1);
		if length(sParent) <= 0 then	
			raise exception 'Impossible de trouver le compte parent pour %',p_account;
		end if;

	end loop;

	return nParent;
end;
$$ language plpgsql volatile;
-- Function: account_update()

-- DROP FUNCTION account_update();

CREATE OR REPLACE FUNCTION account_update(p_f_id fiche.f_id%type,p_account tmp_pcmn.pcm_val%type)
  RETURNS int4 AS
$BODY$
-- account_update
-- parameter f_id, pcm_val
-- purpose update the account of a card and create it into PCMN if it doesn't exist yet
-- 
declare
nMax fiche.f_id%type;
nCount integer;
nParent tmp_pcmn.pcm_val_parent%type;
sName varchar;
nJft_id attr_value.jft_id%type;
begin
	
	-- if p_value empty
	if length(trim(p_account)) != 0 then
	-- does the account exist ?
		select count(*) into nCount from tmp_pcmn where pcm_val=p_account;
		if nCount = 0 then
		-- retrieve name
		select av_text into sName from 
			attr_value join jnt_fic_att_value using (jft_id)
			where
			ad_id=1 and f_id=p_f_id;
		-- get parent
		nParent:=fiche_account_parent(p_f_id);
		-- account doesn't exist we need to add id
		insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (p_account,sName,nParent);
		end if;		
	end if;
	-- we retrieve jft_id
	select jft_id into njft_id from jnt_fic_att_value where f_id=p_f_id and ad_id=5;
	-- we update the account
	update attr_value set av_text=p_account where jft_id=njft_id;
		
return njft_id;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

create or replace function account_add (p_id tmp_pcmn.pcm_val%type,p_name varchar)
returns void
as
$$
-- account_add (p_id tmp_pcmn.pcm_val%type,p_name varchar)
-- parameter 
-- p_id id of the account
-- name account's name
-- purpose insert a new account if it doesn't exist yet
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	nCount integer;
begin
	select count(*) into nCount from tmp_pcmn where pcm_val=p_id;
	if nCount = 0 then
		nParent=account_parent(p_id);
		insert into tmp_pcmn (pcm_val,pcm_lib,pcm_val_parent)
			values (p_id, p_name,nParent);
	end if;
return;
end ;
$$ language plpgsql;


create table document_type (
	dt_id serial primary key,
	dt_value varchar(80)
);

comment on table document_type is 'Type of document : meeting, invoice,...';
CREATE or replace FUNCTION t_document_type_insert() RETURNS trigger AS $body$
    BEGIN
        execute  'create sequence seq_doc_type_'||NEW.dt_id;
raise notice 'Creating sequence seq_doc_type_%',NEW.dt_id;
        RETURN NEW;
    END;
$body$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_document_type_i after INSERT oN document_type
    FOR EACH ROW EXECUTE PROCEDURE t_document_type_insert();

INSERT INTO document_type VALUES (1,'Document Interne');
INSERT INTO document_type VALUES (2,'Bons de commande client');
INSERT INTO document_type VALUES (3,'Bon de commande Fournisseur');
INSERT INTO document_type VALUES (4,'Facture');
INSERT INTO document_type VALUES (5,'Lettre de rappel');
INSERT INTO document_type VALUES (6,'Courrier');
INSERT INTO document_type VALUES (7,'Proposition');
INSERT INTO document_type VALUES (8,'Email');
INSERT INTO document_type VALUES (9,'Divers');
alter sequence document_type_dt_id_seq restart with 10;

create table document_modele (
	md_id serial primary key,
	md_name text not null,
	md_lob oid,
	md_type integer not null ,
	md_filename text,
	md_mimetype text
);


comment on table document_modele is ' contains all the template for the  documents';

alter table document_modele add constraint md_type foreign key (md_type) references document_type(dt_id);



create or replace function card_class_base(p_f_id fiche.f_id%type) 
returns fiche_def.fd_class_base%type
as 
$$

declare
	n_poste fiche_def.fd_class_base%type;
begin
-- card_class_base (integer)
-- param: $1 fiche.f_id
-- purpose : retrieve the class of a card
-- 

	select fd_class_base into n_poste from fiche_def join fiche using (fd_id)
	where f_id=p_f_id;
	if not FOUND then 
		raise exception 'Invalid fiche card_class_base(%)',p_f_id;
	end if;
return n_poste;
end;
$$ language plpgsql;

-- fiche_account_parent
create or replace function fiche_account_parent(p_f_id integer) 
returns poste_comptable as $$
declare
-- fiche_account_parent returns the fd_class_base
-- parameter f_id (from fiche)
ret poste_comptable;
begin
	select fd_class_base into ret from fiche_def join fiche using (fd_id) where f_id=p_f_id;
	if not FOUND then
		raise exception '% N''existe pas',p_f_id;
	end if;
	return ret;
end;
$$
language plpgsql ;
delete from form where fo_fr_id=3000000;
delete from formdef where fr_id=3000000;

INSERT INTO formdef (fr_id, fr_label) VALUES (3000000, 'TVA déclaration Belge');
--
-- Data for TOC entry 2 (OID 315304)
-- Name: formdef; Type: TABLE DATA; Schema: public; Owner: dany
--
--

INSERT INTO form VALUES (3000398, 3000000, 1, 'Prestation [ case 03 ]', '[700%]-[7000005]');
INSERT INTO form VALUES (3000399, 3000000, 2, 'Prestation intra [ case 47 ]', '[7000005]');
INSERT INTO form VALUES (3000400, 3000000, 3, 'Tva due   [case 54]', '[4513]+[4512]+[4511] FROM=01.2005');
INSERT INTO form VALUES (3000401, 3000000, 4, 'Marchandises, matière première et auxiliaire [case 81 ]', '[60%]');
INSERT INTO form VALUES (3000402, 3000000, 7, 'Service et bien divers [case 82]', '[61%]');
INSERT INTO form VALUES (3000403, 3000000, 8, 'bien d''invest [ case 83 ]', '[2400%]');
INSERT INTO form VALUES (3000404, 3000000, 9, 'TVA déductible [ case 59 ]', 'abs([4117]-[411%])');
INSERT INTO form VALUES (3000405, 3000000, 8, 'TVA non ded -> voiture', '[610022]*0.21/2');
INSERT INTO form VALUES (3000406, 3000000, 9, 'Acompte TVA', '[4117]');

-- create the table document

create table document 
(
	d_id	serial primary key,
	ag_id	int4 not null,
	d_lob oid,
	d_number int8 not null,
	d_filename text,
	d_mimetype text
);



comment on table document is 'This table contains all the documents : summary and lob files';

create sequence document_seq;

comment on sequence document_seq is 'Sequence for the sequence bound to the document modele';

CREATE TABLE document_state (
    s_id serial NOT NULL,
    s_value character varying(50) NOT NULL
);


COMMENT ON TABLE document_state IS 'State of the document';

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('document_state', 's_id'), 3, true);


INSERT INTO document_state VALUES (1, 'Envoyé');
INSERT INTO document_state VALUES (2, 'Brouillon');
INSERT INTO document_state VALUES (3, 'A envoyer');
INSERT INTO document_state VALUES (4, 'Reçu');

ALTER TABLE ONLY document_state ADD CONSTRAINT document_state_pkey PRIMARY KEY (s_id);
alter sequence s_attr_def restart with 24;
insert into attr_def (ad_text) values ('Ville');
insert into attr_min values(9,24);
insert into attr_min values(8,24);
insert into attr_min values(14,24);
-- upgrade all customer 
insert into jnt_fic_attr select fd_id,24 from jnt_fic_attr join fiche_def using (fd_id) where frd_id=9 and ad_id=1;
-- supplier
insert into jnt_fic_attr select fd_id,24 from jnt_fic_attr join fiche_def using (fd_id) where frd_id=8 and ad_id=1;
-- administration

insert into jnt_fic_attr select fd_id,24 from jnt_fic_attr join fiche_def using (fd_id) where frd_id=9 and ad_id=14;

-- 
create table action_gestion (
	ag_id serial primary key,
	ag_type  int4,
	f_id_dest int4 not null,
	f_id_exp int4 not null,
	ag_title varchar(70),
	ag_timestamp timestamp default now(),
	ag_cal char(1) default 'C',
	ag_ref_ag_id int4,
	ag_comment text
);

comment on table action_gestion is 'Action for Managing';


-- add contact
alter sequence s_fiche_def_ref restart 16;
insert into fiche_def_ref(frd_text) values ('Contact'); 

insert into attr_def(ad_text) values ('Société'); 
insert into attr_def(ad_text) values ('Fax');
insert into attr_min values(16,1);
insert into attr_min values(16,17);
insert into attr_min values(16,18);
insert into attr_min values(16,25);
insert into attr_min values(16,26);
insert into attr_def (ad_text) values ('GSM'); 
insert into attr_min values(16,27);

CREATE or replace FUNCTION t_jrn_def_sequence() RETURNS trigger AS $body$
    BEGIN
        execute  'create sequence s_jrn_'||NEW.jrn_def_id;
raise notice 'Creating sequence s_jrn_%',NEW.jrn_def_id;
        RETURN NEW;
    END;
$body$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_jrn_def_sequence_i after INSERT oN jrn_def
    FOR EACH ROW EXECUTE PROCEDURE t_jrn_def_sequence();

create view vw_supplier as SELECT a.f_id, a.av_text AS name, a1.av_text AS quick_code, b.av_text AS tva_num, c.av_text AS poste_comptable, d.av_text AS rue, e.av_text AS code_postal, f.av_text AS pays, g.av_text AS telephone, h.av_text AS email
   FROM ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
           FROM fiche
      JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 1) a
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
           FROM fiche
      JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 13) b USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
      FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 23) a1 USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 5) c USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 14) d USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 15) e USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 16) f USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 17) g USING (f_id)
   LEFT JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 18) h USING (f_id)
  WHERE a.frd_id = 8;


insert into parameter (pr_id) values ('MY_TEL');
insert into parameter (pr_id) values ('MY_PAYS');
insert into parameter (pr_id) values ('MY_FAX');
alter table document add d_state int;
alter table action_gestion add ag_ref text;

create  unique index k_ag_ref on action_gestion(ag_ref);
update version set val=14;
insert into action values(28,'Module Suivi Document');
insert into action values(22,'Module Client');
insert into action values (24,'Module Fournisseur');
insert into action values (26,'Module Administration');
insert into action values (30,'Module Gestion');

insert into format_csv_banque values ('Argenta Belgique','argenta_be.inc.php');
insert into format_csv_banque values ('CBC Belgique','cbc_be.inc.php');
CREATE SEQUENCE s_cbc
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

commit;
