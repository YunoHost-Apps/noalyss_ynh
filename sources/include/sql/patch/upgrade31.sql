begin ;

CREATE or replace FUNCTION insert_quant_sold(
       p_internal text, 
	   p_jid numeric,
       p_fiche character varying, 
       p_quant numeric, 
       p_price numeric, 
       p_vat numeric, 
       p_vat_code integer, 
       p_client character varying) 
       RETURNS void
    AS $$
declare
        fid_client integer;
        fid_good   integer;
begin

        select f_id into fid_client from
                attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(p_client);
        select f_id into fid_good from
                attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(p_fiche);
        insert into quant_sold
                (qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client,qs_valid)
        values
                (p_internal,p_jid,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client,'Y');
        return;
end;
 $$
    LANGUAGE plpgsql;



CREATE or REPLACE FUNCTION account_update(p_f_id integer, p_account poste_comptable) RETURNS integer
    AS $$
declare
nMax fiche.f_id%type;
nCount integer;
nParent tmp_pcmn.pcm_val_parent%type;
sName varchar;
nJft_id attr_value.jft_id%type;
begin
	
	if length(trim(p_account)) != 0 then
		select count(*) into nCount from tmp_pcmn where pcm_val=p_account;
		if nCount = 0 then
		select av_text into sName from 
			attr_value join jnt_fic_att_value using (jft_id)
			where
			ad_id=1 and f_id=p_f_id;
		nParent:=account_parent(p_account);
		insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (p_account,sName,nParent);
		end if;		
	end if;
	select jft_id into njft_id from jnt_fic_att_value where f_id=p_f_id and ad_id=5;
	update attr_value set av_text=p_account where jft_id=njft_id;
		
return njft_id;
end;
$$
    LANGUAGE plpgsql;



CREATE TABLE quant_purchase (
    qp_id integer DEFAULT nextval(('s_quantity'::text)::regclass) NOT NULL,
    qp_internal text NOT NULL,
	j_id integer not null,	 
    qp_fiche integer NOT NULL,
    qp_quantite numeric(20,4) NOT NULL,
    qp_price numeric(20,4),
    qp_vat numeric(20,4) default 0.0,
    qp_vat_code integer,
    qp_nd_amount numeric(20,4) default 0.0,
    qp_nd_tva numeric(20,4) default 0.0,
    qp_nd_tva_recup numeric(20,4) default 0.0,
    qp_supplier integer NOT NULL,
    qp_valid char(1) default 'Y' not null
);
ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT qp_id_pk PRIMARY KEY (qp_id);

ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT quant_purchase_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;

---
truncate quant_sold;
alter table quant_sold ADD qs_valid char(1) ;
alter table quant_sold add j_id integer;
alter table quant_sold alter j_id set not null;

ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT quant_sold_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;

update  quant_sold set qs_valid='Y';
alter table quant_sold alter qs_valid set default 'Y';
alter table quant_sold alter qs_valid set not null;



CREATE or replace FUNCTION insert_quant_purchase
       (p_internal text, 
	   p_j_id numeric,
       p_fiche character varying, 
       p_quant numeric, 
       p_price numeric, 
       p_vat numeric, 
       p_vat_code integer, 
       p_nd_amount numeric, 
       p_nd_tva numeric,
       p_nd_tva_recup numeric,	
       p_client character varying) RETURNS void
    AS $$
declare
        fid_client integer;
        fid_good   integer;
begin
        select f_id into fid_client from
                attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(p_client);
        select f_id into fid_good from
                attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(p_fiche);
        insert into quant_purchase
                (qp_internal,
				j_id,
		qp_fiche,
		qp_quantite,
		qp_price,
		qp_vat,
		qp_vat_code,
		qp_nd_amount,
		qp_nd_tva,
		qp_nd_tva_recup,
		qp_supplier)
        values
                (p_internal,
				p_j_id,
		fid_good,
		p_quant,
		p_price,
		p_vat,
		p_vat_code,
		p_nd_amount,
		p_nd_tva,
		p_nd_tva_recup,
		fid_client);
        return;
end;
 $$
    LANGUAGE plpgsql;
insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values(9,'Comptes hors Compta',0);

COMMENT ON TABLE parameter IS 'parameter of the company';

--
-- Name: plan_analytique; Type: TABLE; Schema: public; Owner: phpcompta; Tablespace: 
--

CREATE TABLE plan_analytique (
    pa_id integer NOT NULL,
    pa_name text DEFAULT 'Sans Nom'::text NOT NULL,
    pa_description text
);

--
-- Name: TABLE plan_analytique; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON TABLE plan_analytique IS 'Plan Analytique (max 5)';


--
-- Name: plan_analytique_pa_id_seq; Type: SEQUENCE; Schema: public; Owner: phpcompta
--

CREATE SEQUENCE plan_analytique_pa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: pa_id; Type: DEFAULT; Schema: public; Owner: phpcompta
--

ALTER TABLE plan_analytique ALTER COLUMN pa_id SET DEFAULT nextval('plan_analytique_pa_id_seq'::regclass);


--
-- Name: plan_analytique_pa_name_key; Type: CONSTRAINT; Schema: public; Owner: phpcompta; Tablespace: 
--

ALTER TABLE ONLY plan_analytique
    ADD CONSTRAINT plan_analytique_pa_name_key UNIQUE (pa_name);


--
-- Name: plan_analytique_pkey; Type: CONSTRAINT; Schema: public; Owner: phpcompta; Tablespace: 
--

ALTER TABLE ONLY plan_analytique
    ADD CONSTRAINT plan_analytique_pkey PRIMARY KEY (pa_id);


-- Ajout table operation_analytique

-- Ajout table poste_analytique
--
-- Name: poste_analytique; Type: TABLE; Schema: public; Owner: phpcompta; Tablespace: 
--

CREATE TABLE poste_analytique (
    po_id integer NOT NULL,
    po_name text NOT NULL,
    pa_id integer NOT NULL,
    po_amount numeric(20,4) DEFAULT 0.0 NOT NULL,
    po_description text
);



--
-- Name: TABLE poste_analytique; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON TABLE poste_analytique IS 'Poste Analytique';


--
-- Name: poste_analytique_po_id_seq; Type: SEQUENCE; Schema: public; Owner: phpcompta
--

CREATE SEQUENCE poste_analytique_po_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;






--
-- Name: po_id; Type: DEFAULT; Schema: public; Owner: phpcompta
--

ALTER TABLE poste_analytique ALTER COLUMN po_id SET DEFAULT nextval('poste_analytique_po_id_seq'::regclass);


--
-- Name: poste_analytique_pkey; Type: CONSTRAINT; Schema: public; Owner: phpcompta; Tablespace: 
--

ALTER TABLE ONLY poste_analytique
    ADD CONSTRAINT poste_analytique_pkey PRIMARY KEY (po_id);


--
-- Name: poste_analytique_pa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: phpcompta
--

ALTER TABLE ONLY poste_analytique
    ADD CONSTRAINT poste_analytique_pa_id_fkey FOREIGN KEY (pa_id) REFERENCES plan_analytique(pa_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: operation_analytique; Type: TABLE; Schema: public; Owner: phpcompta; Tablespace: 
--
create sequence s_oa_group;

CREATE TABLE operation_analytique (
    oa_id integer NOT NULL,
    po_id integer NOT NULL,
    pa_id integer not null,
    oa_amount numeric(20,4) NOT NULL,
    oa_description text,
    oa_debit boolean DEFAULT true NOT NULL,
    j_id integer,
    oa_group integer DEFAULT nextval('s_oa_group'::regclass) NOT NULL,
    oa_date date NOT NULL
);



--
-- Name: TABLE operation_analytique; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON TABLE operation_analytique IS 'History of the analytic account';


--
-- Name: historique_analytique_ha_id_seq; Type: SEQUENCE; Schema: public; Owner: phpcompta
--

CREATE SEQUENCE historique_analytique_ha_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: oa_id; Type: DEFAULT; Schema: public; Owner: phpcompta
--

ALTER TABLE operation_analytique ALTER COLUMN oa_id SET DEFAULT nextval('historique_analytique_ha_id_seq'::regclass);


--
-- Name: historique_analytique_pkey; Type: CONSTRAINT; Schema: public; Owner: phpcompta; Tablespace: 
--

ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT historique_analytique_pkey PRIMARY KEY (oa_id);


--
-- Name: operation_analytique_j_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: phpcompta
--

ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT operation_analytique_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: operation_analytique_po_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: phpcompta
--

ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT operation_analytique_po_id_fkey FOREIGN KEY (po_id) REFERENCES poste_analytique(po_id) ON UPDATE CASCADE ON DELETE CASCADE;

INSERT INTO parameter VALUES ('MY_ANALYTIC', 'nu');

alter table jrn add constraint ux_internal unique (jr_internal);

alter table user_sec_jrn add constraint uj_priv_id_fkey foreign key(uj_jrn_id) references jrn_def(jrn_def_id) on update cascade on delete cascade;
alter table user_sec_jrn drop constraint "$1";
alter table operation_analytique add oa_row int4;

create or replace function upper_po_name() returns trigger as $$
declare
   name text;
begin
   name:=upper(NEW.po_name);
   name:=trim(name);
   name:=replace(name,' ','');		
   NEW.po_name:=name;

return NEW;
end;
$$ LANGUAGE plpgsql;

create or replace function upper_pa_name() returns trigger as $$
declare
   name text;
begin
   name:=upper(NEW.pa_name);
   name:=trim(name);
   name:=replace(name,' ','');
   NEW.pa_name:=name;
return NEW;
end;
$$ LANGUAGE plpgsql;


CREATE TRIGGER t_upper_po_name BEFORE INSERT OR UPDATE  ON poste_analytique
    FOR EACH ROW EXECUTE PROCEDURE upper_po_name();

CREATE TRIGGER t_upper_pa_name before INSERT OR UPDATE on plan_analytique
    FOR EACH ROW EXECUTE PROCEDURE upper_pa_name();

create unique index ux_po_name on poste_analytique (po_name);

insert into parameter (pr_id,pr_value) select distinct 'MY_COUNTRY',pcm_country from tmp_pcmn limit 1;

alter table tmp_pcmn drop pcm_country;


CREATE TABLE bilan (
    b_id integer NOT NULL,
    b_name text NOT NULL,
    b_file_template text NOT NULL,
    b_file_form text,
    b_type text NOT NULL
);



--
-- Name: TABLE bilan; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON TABLE bilan IS 'contains the template and the data for generating different documents  ';


--
-- Name: COLUMN bilan.b_id; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON COLUMN bilan.b_id IS 'primary key';


--
-- Name: COLUMN bilan.b_name; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON COLUMN bilan.b_name IS 'Name of the document';


--
-- Name: COLUMN bilan.b_file_template; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON COLUMN bilan.b_file_template IS 'path of the template (document/...)';


--
-- Name: COLUMN bilan.b_file_form; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON COLUMN bilan.b_file_form IS 'path of the file with forms';


--
-- Name: COLUMN bilan.b_type; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON COLUMN bilan.b_type IS 'type = ODS, RTF...';


--
-- Name: bilan_b_id_seq; Type: SEQUENCE; Schema: public; Owner: phpcompta
--

CREATE SEQUENCE bilan_b_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;




--
-- Name: bilan_b_id_seq; Type: SEQUENCE SET; Schema: public; Owner: phpcompta
--

SELECT pg_catalog.setval('bilan_b_id_seq', 4, true);


--
-- Name: b_id; Type: DEFAULT; Schema: public; Owner: phpcompta
--

ALTER TABLE bilan ALTER COLUMN b_id SET DEFAULT nextval('bilan_b_id_seq'::regclass);


--
-- Data for Name: bilan; Type: TABLE DATA; Schema: public; Owner: phpcompta
--

INSERT INTO bilan VALUES (1, 'Bilan Belge complet', 'document/fr_be/bnb.rtf', 'document/fr_be/bnb.form', 'RTF');


--
-- Name: bilan_b_name_key; Type: CONSTRAINT; Schema: public; Owner: phpcompta; Tablespace: 
--

ALTER TABLE ONLY bilan
    ADD CONSTRAINT bilan_b_name_key UNIQUE (b_name);


--
-- Name: bilan_pkey; Type: CONSTRAINT; Schema: public; Owner: phpcompta; Tablespace: 
--

ALTER TABLE ONLY bilan
    ADD CONSTRAINT bilan_pkey PRIMARY KEY (b_id);


--
-- PostgreSQL database dump complete
--
CREATE TABLE op_predef (
    od_id integer NOT NULL,
    jrn_def_id integer NOT NULL,
    od_name text NOT NULL,
    od_item integer NOT NULL,
    od_jrn_type text NOT NULL
);



--
-- Name: TABLE op_predef; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON TABLE op_predef IS 'predefined operation';


--
-- Name: COLUMN op_predef.jrn_def_id; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON COLUMN op_predef.jrn_def_id IS 'jrn_id';


--
-- Name: COLUMN op_predef.od_name; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON COLUMN op_predef.od_name IS 'name of the operation';


--
-- Name: op_def_op_seq; Type: SEQUENCE; Schema: public; Owner: phpcompta
--

CREATE SEQUENCE op_def_op_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



--
-- Name: od_id; Type: DEFAULT; Schema: public; Owner: phpcompta
--

ALTER TABLE op_predef ALTER COLUMN od_id SET DEFAULT nextval('op_def_op_seq'::regclass);


--
-- Name: op_def_op_name_key; Type: CONSTRAINT; Schema: public; Owner: phpcompta; Tablespace: 
--

ALTER TABLE ONLY op_predef
    ADD CONSTRAINT op_def_op_name_key UNIQUE (od_name,jrn_def_id);


--
-- Name: op_def_pkey; Type: CONSTRAINT; Schema: public; Owner: phpcompta; Tablespace: 
--

ALTER TABLE ONLY op_predef
    ADD CONSTRAINT op_def_pkey PRIMARY KEY (od_id);


--
-- Name: jrn_def_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: phpcompta
--

ALTER TABLE ONLY op_predef
    ADD CONSTRAINT jrn_def_id_fk FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE TABLE op_predef_detail (
    opd_id integer NOT NULL,
    od_id integer NOT NULL,
    opd_poste text NOT NULL,
    opd_amount numeric(20,4),
    opd_tva_id integer,
    opd_quantity numeric(20,4),
    opd_debit boolean NOT NULL,
    opd_tva_amount numeric(20,4),
    opd_comment text
);



--
-- Name: TABLE op_predef_detail; Type: COMMENT; Schema: public; Owner: phpcompta
--

COMMENT ON TABLE op_predef_detail IS 'contains the detail of predefined operations';


--
-- Name: op_predef_detail_opd_id_seq; Type: SEQUENCE; Schema: public; Owner: phpcompta
--

CREATE SEQUENCE op_predef_detail_opd_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: opd_id; Type: DEFAULT; Schema: public; Owner: phpcompta
--

ALTER TABLE op_predef_detail ALTER COLUMN opd_id SET DEFAULT nextval('op_predef_detail_opd_id_seq'::regclass);


--
-- Name: op_predef_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: phpcompta; Tablespace: 
--

ALTER TABLE ONLY op_predef_detail
    ADD CONSTRAINT op_predef_detail_pkey PRIMARY KEY (opd_id);

INSERT INTO "action" VALUES (50, 'Definir les Plans Analytiques et les postes');
INSERT INTO "action" VALUES (51, 'Impression CA');
INSERT INTO "action" VALUES (52, 'Operations Diverses CA');
update version set val=32;

commit;
