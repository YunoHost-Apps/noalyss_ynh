 SET client_encoding = 'utf8';
 SET check_function_bodies = false;
 SET client_min_messages = warning;
SET search_path = public, pg_catalog;
ALTER TABLE action_detail ALTER COLUMN ad_id SET DEFAULT nextval('action_detail_ad_id_seq'::regclass);
ALTER TABLE del_action ALTER COLUMN del_id SET DEFAULT nextval('del_action_del_id_seq'::regclass);
ALTER TABLE extension ALTER COLUMN ex_id SET DEFAULT nextval('extension_ex_id_seq'::regclass);
ALTER TABLE forecast ALTER COLUMN f_id SET DEFAULT nextval('forecast_f_id_seq'::regclass);
ALTER TABLE forecast_cat ALTER COLUMN fc_id SET DEFAULT nextval('forecast_cat_fc_id_seq'::regclass);
ALTER TABLE forecast_item ALTER COLUMN fi_id SET DEFAULT nextval('forecast_item_fi_id_seq'::regclass);
ALTER TABLE jnt_letter ALTER COLUMN jl_id SET DEFAULT nextval('jnt_letter_jl_id_seq'::regclass);
ALTER TABLE jrn_info ALTER COLUMN ji_id SET DEFAULT nextval('jrn_info_ji_id_seq'::regclass);
ALTER TABLE letter_cred ALTER COLUMN lc_id SET DEFAULT nextval('letter_cred_lc_id_seq'::regclass);
ALTER TABLE letter_deb ALTER COLUMN ld_id SET DEFAULT nextval('letter_deb_ld_id_seq'::regclass);
ALTER TABLE mod_payment ALTER COLUMN mp_id SET DEFAULT nextval('mod_payment_mp_id_seq'::regclass);
ALTER TABLE user_sec_extension ALTER COLUMN use_id SET DEFAULT nextval('user_sec_extension_use_id_seq'::regclass);
ALTER TABLE ONLY action_detail
    ADD CONSTRAINT action_detail_pkey PRIMARY KEY (ad_id);
ALTER TABLE ONLY action_gestion
    ADD CONSTRAINT action_gestion_pkey PRIMARY KEY (ag_id);
ALTER TABLE ONLY action
    ADD CONSTRAINT action_pkey PRIMARY KEY (ac_id);
ALTER TABLE ONLY attr_def
    ADD CONSTRAINT attr_def_pkey PRIMARY KEY (ad_id);
ALTER TABLE ONLY bilan
    ADD CONSTRAINT bilan_b_name_key UNIQUE (b_name);
ALTER TABLE ONLY bilan
    ADD CONSTRAINT bilan_pkey PRIMARY KEY (b_id);
ALTER TABLE ONLY centralized
    ADD CONSTRAINT centralized_pkey PRIMARY KEY (c_id);
ALTER TABLE ONLY del_action
    ADD CONSTRAINT del_action_pkey PRIMARY KEY (del_id);
ALTER TABLE ONLY document_modele
    ADD CONSTRAINT document_modele_pkey PRIMARY KEY (md_id);
ALTER TABLE ONLY document
    ADD CONSTRAINT document_pkey PRIMARY KEY (d_id);
ALTER TABLE ONLY document_state
    ADD CONSTRAINT document_state_pkey PRIMARY KEY (s_id);
ALTER TABLE ONLY document_type
    ADD CONSTRAINT document_type_pkey PRIMARY KEY (dt_id);
ALTER TABLE ONLY fiche_def
    ADD CONSTRAINT fiche_def_pkey PRIMARY KEY (fd_id);
ALTER TABLE ONLY fiche_def_ref
    ADD CONSTRAINT fiche_def_ref_pkey PRIMARY KEY (frd_id);
ALTER TABLE ONLY fiche
    ADD CONSTRAINT fiche_pkey PRIMARY KEY (f_id);
ALTER TABLE ONLY forecast_cat
    ADD CONSTRAINT forecast_cat_pk PRIMARY KEY (fc_id);
ALTER TABLE ONLY forecast_item
    ADD CONSTRAINT forecast_item_pkey PRIMARY KEY (fi_id);
ALTER TABLE ONLY forecast
    ADD CONSTRAINT forecast_pk PRIMARY KEY (f_id);
ALTER TABLE ONLY form
    ADD CONSTRAINT form_pkey PRIMARY KEY (fo_id);
ALTER TABLE ONLY format_csv_banque
    ADD CONSTRAINT format_csv_banque_pkey PRIMARY KEY (name);
ALTER TABLE ONLY formdef
    ADD CONSTRAINT formdef_pkey PRIMARY KEY (fr_id);
ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT historique_analytique_pkey PRIMARY KEY (oa_id);
ALTER TABLE ONLY extension
    ADD CONSTRAINT idx_ex_code UNIQUE (ex_code);
ALTER TABLE ONLY info_def
    ADD CONSTRAINT info_def_pkey PRIMARY KEY (id_type);
ALTER TABLE ONLY del_jrnx
    ADD CONSTRAINT j_id PRIMARY KEY (j_id);
ALTER TABLE ONLY jnt_fic_att_value
    ADD CONSTRAINT jnt_fic_att_value_pkey PRIMARY KEY (jft_id);
ALTER TABLE ONLY jnt_letter
    ADD CONSTRAINT jnt_letter_pk PRIMARY KEY (jl_id);
ALTER TABLE ONLY del_jrn
    ADD CONSTRAINT jr_id PRIMARY KEY (jr_id);
ALTER TABLE ONLY jrn_action
    ADD CONSTRAINT jrn_action_pkey PRIMARY KEY (ja_id);
ALTER TABLE ONLY jrn_def
    ADD CONSTRAINT jrn_def_jrn_def_name_key UNIQUE (jrn_def_name);
ALTER TABLE ONLY jrn_def
    ADD CONSTRAINT jrn_def_pkey PRIMARY KEY (jrn_def_id);
ALTER TABLE ONLY jrn_info
    ADD CONSTRAINT jrn_info_pkey PRIMARY KEY (ji_id);
ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_periode_pk PRIMARY KEY (jrn_def_id, p_id);
ALTER TABLE ONLY jrn
    ADD CONSTRAINT jrn_pkey PRIMARY KEY (jr_id, jr_def_id);
ALTER TABLE ONLY jrn_rapt
    ADD CONSTRAINT jrn_rapt_pkey PRIMARY KEY (jra_id);
ALTER TABLE ONLY jrn_type
    ADD CONSTRAINT jrn_type_pkey PRIMARY KEY (jrn_type_id);
ALTER TABLE ONLY jrnx
    ADD CONSTRAINT jrnx_pkey PRIMARY KEY (j_id);
ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT letter_cred_pk PRIMARY KEY (lc_id);
ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT letter_deb_pk PRIMARY KEY (ld_id);
ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_pkey PRIMARY KEY (mp_id);
ALTER TABLE ONLY op_predef
    ADD CONSTRAINT op_def_op_name_key UNIQUE (od_name, jrn_def_id);
ALTER TABLE ONLY op_predef
    ADD CONSTRAINT op_def_pkey PRIMARY KEY (od_id);
ALTER TABLE ONLY op_predef_detail
    ADD CONSTRAINT op_predef_detail_pkey PRIMARY KEY (opd_id);
ALTER TABLE ONLY parameter
    ADD CONSTRAINT parameter_pkey PRIMARY KEY (pr_id);
ALTER TABLE ONLY parm_code
    ADD CONSTRAINT parm_code_pkey PRIMARY KEY (p_code);
ALTER TABLE ONLY parm_money
    ADD CONSTRAINT parm_money_pkey PRIMARY KEY (pm_code);
ALTER TABLE ONLY parm_periode
    ADD CONSTRAINT parm_periode_pkey PRIMARY KEY (p_id);
ALTER TABLE ONLY parm_poste
    ADD CONSTRAINT parm_poste_pkey PRIMARY KEY (p_value);
ALTER TABLE ONLY extension
    ADD CONSTRAINT pk_extension PRIMARY KEY (ex_id);
ALTER TABLE ONLY groupe_analytique
    ADD CONSTRAINT pk_ga_id PRIMARY KEY (ga_id);
ALTER TABLE ONLY jnt_fic_attr
    ADD CONSTRAINT pk_jnt_fic_attr PRIMARY KEY (jnt_id);
ALTER TABLE ONLY user_local_pref
    ADD CONSTRAINT pk_user_local_pref PRIMARY KEY (user_id, parameter_type);
ALTER TABLE ONLY plan_analytique
    ADD CONSTRAINT plan_analytique_pa_name_key UNIQUE (pa_name);
ALTER TABLE ONLY plan_analytique
    ADD CONSTRAINT plan_analytique_pkey PRIMARY KEY (pa_id);
ALTER TABLE ONLY poste_analytique
    ADD CONSTRAINT poste_analytique_pkey PRIMARY KEY (po_id);
ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT qp_id_pk PRIMARY KEY (qp_id);
ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT qs_id_pk PRIMARY KEY (qs_id);
ALTER TABLE ONLY stock_goods
    ADD CONSTRAINT stock_goods_pkey PRIMARY KEY (sg_id);
ALTER TABLE ONLY tmp_pcmn
    ADD CONSTRAINT tmp_pcmn_pkey PRIMARY KEY (pcm_val);
ALTER TABLE ONLY todo_list
    ADD CONSTRAINT todo_list_pkey PRIMARY KEY (tl_id);
ALTER TABLE ONLY tva_rate
    ADD CONSTRAINT tva_id_pk PRIMARY KEY (tva_id);
ALTER TABLE ONLY user_sec_act
    ADD CONSTRAINT user_sec_act_pkey PRIMARY KEY (ua_id);
ALTER TABLE ONLY user_sec_extension
    ADD CONSTRAINT user_sec_extension_ex_id_key UNIQUE (ex_id, use_login);
ALTER TABLE ONLY user_sec_extension
    ADD CONSTRAINT user_sec_extension_pkey PRIMARY KEY (use_id);
ALTER TABLE ONLY user_sec_jrn
    ADD CONSTRAINT user_sec_jrn_pkey PRIMARY KEY (uj_id);
ALTER TABLE ONLY jrn
    ADD CONSTRAINT ux_internal UNIQUE (jr_internal);
ALTER TABLE ONLY centralized
    ADD CONSTRAINT "$1" FOREIGN KEY (c_jrn_def) REFERENCES jrn_def(jrn_def_id);
ALTER TABLE ONLY user_sec_act
    ADD CONSTRAINT "$1" FOREIGN KEY (ua_act_id) REFERENCES action(ac_id);
ALTER TABLE ONLY fiche_def
    ADD CONSTRAINT "$1" FOREIGN KEY (frd_id) REFERENCES fiche_def_ref(frd_id);
ALTER TABLE ONLY attr_min
    ADD CONSTRAINT "$1" FOREIGN KEY (frd_id) REFERENCES fiche_def_ref(frd_id);
ALTER TABLE ONLY fiche
    ADD CONSTRAINT "$1" FOREIGN KEY (fd_id) REFERENCES fiche_def(fd_id);
ALTER TABLE ONLY jnt_fic_att_value
    ADD CONSTRAINT "$1" FOREIGN KEY (f_id) REFERENCES fiche(f_id);
ALTER TABLE ONLY attr_value
    ADD CONSTRAINT "$1" FOREIGN KEY (jft_id) REFERENCES jnt_fic_att_value(jft_id);
ALTER TABLE ONLY jnt_fic_attr
    ADD CONSTRAINT "$1" FOREIGN KEY (fd_id) REFERENCES fiche_def(fd_id);
ALTER TABLE ONLY jrn
    ADD CONSTRAINT "$1" FOREIGN KEY (jr_def_id) REFERENCES jrn_def(jrn_def_id);
ALTER TABLE ONLY jrn_action
    ADD CONSTRAINT "$1" FOREIGN KEY (ja_jrn_type) REFERENCES jrn_type(jrn_type_id);
ALTER TABLE ONLY jrn_def
    ADD CONSTRAINT "$1" FOREIGN KEY (jrn_def_type) REFERENCES jrn_type(jrn_type_id);
ALTER TABLE ONLY jrnx
    ADD CONSTRAINT "$2" FOREIGN KEY (j_jrn_def) REFERENCES jrn_def(jrn_def_id);
ALTER TABLE ONLY attr_min
    ADD CONSTRAINT "$2" FOREIGN KEY (ad_id) REFERENCES attr_def(ad_id);
ALTER TABLE ONLY jnt_fic_att_value
    ADD CONSTRAINT "$2" FOREIGN KEY (ad_id) REFERENCES attr_def(ad_id);
ALTER TABLE ONLY jnt_fic_attr
    ADD CONSTRAINT "$2" FOREIGN KEY (ad_id) REFERENCES attr_def(ad_id);
ALTER TABLE ONLY action_detail
    ADD CONSTRAINT action_detail_ag_id_fkey FOREIGN KEY (ag_id) REFERENCES action_gestion(ag_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY forecast_item
    ADD CONSTRAINT card FOREIGN KEY (fi_card) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY forecast_item
    ADD CONSTRAINT fk_forecast FOREIGN KEY (fc_id) REFERENCES forecast_cat(fc_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_info
    ADD CONSTRAINT fk_info_def FOREIGN KEY (id_type) REFERENCES info_def(id_type) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_info
    ADD CONSTRAINT fk_jrn FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY groupe_analytique
    ADD CONSTRAINT fk_pa_id FOREIGN KEY (pa_id) REFERENCES plan_analytique(pa_id) ON DELETE CASCADE;
ALTER TABLE ONLY jrnx
    ADD CONSTRAINT fk_pcmn_val FOREIGN KEY (j_poste) REFERENCES tmp_pcmn(pcm_val);
ALTER TABLE ONLY centralized
    ADD CONSTRAINT fk_pcmn_val FOREIGN KEY (c_poste) REFERENCES tmp_pcmn(pcm_val);
ALTER TABLE ONLY stock_goods
    ADD CONSTRAINT fk_stock_good_f_id FOREIGN KEY (f_id) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY forecast_cat
    ADD CONSTRAINT forecast_child FOREIGN KEY (f_id) REFERENCES forecast(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY form
    ADD CONSTRAINT formdef_fk FOREIGN KEY (fo_fr_id) REFERENCES formdef(fr_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT jnt_cred_fk FOREIGN KEY (jl_id) REFERENCES jnt_letter(jl_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT jnt_deb_fk FOREIGN KEY (jl_id) REFERENCES jnt_letter(jl_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY op_predef
    ADD CONSTRAINT jrn_def_id_fk FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_per_jrn_def_id FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_periode_p_id FOREIGN KEY (p_id) REFERENCES parm_periode(p_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT letter_cred_fk FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT letter_deb_fk FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY document_modele
    ADD CONSTRAINT md_type FOREIGN KEY (md_type) REFERENCES document_type(dt_id);
ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_mp_fd_id_fkey FOREIGN KEY (mp_fd_id) REFERENCES fiche_def(fd_id);
ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_mp_jrn_def_id_fkey FOREIGN KEY (mp_jrn_def_id) REFERENCES jrn_def(jrn_def_id);
ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT operation_analytique_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT operation_analytique_po_id_fkey FOREIGN KEY (po_id) REFERENCES poste_analytique(po_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY poste_analytique
    ADD CONSTRAINT poste_analytique_pa_id_fkey FOREIGN KEY (pa_id) REFERENCES plan_analytique(pa_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT qp_vat_code_fk FOREIGN KEY (qp_vat_code) REFERENCES tva_rate(tva_id);
ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT qs_vat_code_fk FOREIGN KEY (qs_vat_code) REFERENCES tva_rate(tva_id);
ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT quant_purchase_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT quant_sold_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY user_sec_jrn
    ADD CONSTRAINT uj_priv_id_fkey FOREIGN KEY (uj_jrn_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;
