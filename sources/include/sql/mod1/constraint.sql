 SET client_encoding = 'utf8';
 SET check_function_bodies = false;
 SET client_min_messages = warning;
SET search_path = public, pg_catalog;
ALTER TABLE ONLY action_detail ALTER COLUMN ad_id SET DEFAULT nextval('action_detail_ad_id_seq'::regclass);
ALTER TABLE ONLY action_gestion_comment ALTER COLUMN agc_id SET DEFAULT nextval('action_gestion_comment_agc_id_seq'::regclass);
ALTER TABLE ONLY action_gestion_operation ALTER COLUMN ago_id SET DEFAULT nextval('action_gestion_operation_ago_id_seq'::regclass);
ALTER TABLE ONLY action_gestion_related ALTER COLUMN aga_id SET DEFAULT nextval('action_gestion_related_aga_id_seq'::regclass);
ALTER TABLE ONLY action_person ALTER COLUMN ap_id SET DEFAULT nextval('action_person_ap_id_seq'::regclass);
ALTER TABLE ONLY action_tags ALTER COLUMN at_id SET DEFAULT nextval('action_tags_at_id_seq'::regclass);
ALTER TABLE ONLY bookmark ALTER COLUMN b_id SET DEFAULT nextval('bookmark_b_id_seq'::regclass);
ALTER TABLE ONLY del_action ALTER COLUMN del_id SET DEFAULT nextval('del_action_del_id_seq'::regclass);
ALTER TABLE ONLY del_jrn ALTER COLUMN dj_id SET DEFAULT nextval('del_jrn_dj_id_seq'::regclass);
ALTER TABLE ONLY del_jrnx ALTER COLUMN djx_id SET DEFAULT nextval('del_jrnx_djx_id_seq'::regclass);
ALTER TABLE ONLY extension ALTER COLUMN ex_id SET DEFAULT nextval('extension_ex_id_seq'::regclass);
ALTER TABLE ONLY forecast ALTER COLUMN f_id SET DEFAULT nextval('forecast_f_id_seq'::regclass);
ALTER TABLE ONLY forecast_cat ALTER COLUMN fc_id SET DEFAULT nextval('forecast_cat_fc_id_seq'::regclass);
ALTER TABLE ONLY forecast_item ALTER COLUMN fi_id SET DEFAULT nextval('forecast_item_fi_id_seq'::regclass);
ALTER TABLE ONLY jnt_letter ALTER COLUMN jl_id SET DEFAULT nextval('jnt_letter_jl_id_seq'::regclass);
ALTER TABLE ONLY jrn_info ALTER COLUMN ji_id SET DEFAULT nextval('jrn_info_ji_id_seq'::regclass);
ALTER TABLE ONLY jrn_note ALTER COLUMN n_id SET DEFAULT nextval('jrn_note_n_id_seq'::regclass);
ALTER TABLE ONLY key_distribution ALTER COLUMN kd_id SET DEFAULT nextval('key_distribution_kd_id_seq'::regclass);
ALTER TABLE ONLY key_distribution_activity ALTER COLUMN ka_id SET DEFAULT nextval('key_distribution_activity_ka_id_seq'::regclass);
ALTER TABLE ONLY key_distribution_detail ALTER COLUMN ke_id SET DEFAULT nextval('key_distribution_detail_ke_id_seq'::regclass);
ALTER TABLE ONLY key_distribution_ledger ALTER COLUMN kl_id SET DEFAULT nextval('key_distribution_ledger_kl_id_seq'::regclass);
ALTER TABLE ONLY letter_cred ALTER COLUMN lc_id SET DEFAULT nextval('letter_cred_lc_id_seq'::regclass);
ALTER TABLE ONLY letter_deb ALTER COLUMN ld_id SET DEFAULT nextval('letter_deb_ld_id_seq'::regclass);
ALTER TABLE ONLY link_action_type ALTER COLUMN l_id SET DEFAULT nextval('link_action_type_l_id_seq'::regclass);
ALTER TABLE ONLY menu_default ALTER COLUMN md_id SET DEFAULT nextval('menu_default_md_id_seq'::regclass);
ALTER TABLE ONLY mod_payment ALTER COLUMN mp_id SET DEFAULT nextval('mod_payment_mp_id_seq'::regclass);
ALTER TABLE ONLY profile ALTER COLUMN p_id SET DEFAULT nextval('profile_p_id_seq'::regclass);
ALTER TABLE ONLY profile_menu ALTER COLUMN pm_id SET DEFAULT nextval('profile_menu_pm_id_seq'::regclass);
ALTER TABLE ONLY profile_sec_repository ALTER COLUMN ur_id SET DEFAULT nextval('profile_sec_repository_ur_id_seq'::regclass);
ALTER TABLE ONLY profile_user ALTER COLUMN pu_id SET DEFAULT nextval('profile_user_pu_id_seq'::regclass);
ALTER TABLE ONLY quant_fin ALTER COLUMN qf_id SET DEFAULT nextval('quant_fin_qf_id_seq'::regclass);
ALTER TABLE ONLY stock_change ALTER COLUMN c_id SET DEFAULT nextval('stock_change_c_id_seq'::regclass);
ALTER TABLE ONLY stock_repository ALTER COLUMN r_id SET DEFAULT nextval('stock_repository_r_id_seq'::regclass);
ALTER TABLE ONLY tags ALTER COLUMN t_id SET DEFAULT nextval('tags_t_id_seq'::regclass);
ALTER TABLE ONLY tmp_stockgood ALTER COLUMN s_id SET DEFAULT nextval('tmp_stockgood_s_id_seq'::regclass);
ALTER TABLE ONLY tmp_stockgood_detail ALTER COLUMN d_id SET DEFAULT nextval('tmp_stockgood_detail_d_id_seq'::regclass);
ALTER TABLE ONLY todo_list_shared ALTER COLUMN id SET DEFAULT nextval('todo_list_shared_id_seq'::regclass);
ALTER TABLE ONLY user_active_security ALTER COLUMN id SET DEFAULT nextval('user_active_security_id_seq'::regclass);
ALTER TABLE ONLY user_filter ALTER COLUMN id SET DEFAULT nextval('user_filter_id_seq'::regclass);
ALTER TABLE ONLY user_sec_action_profile ALTER COLUMN ua_id SET DEFAULT nextval('user_sec_action_profile_ua_id_seq'::regclass);
ALTER TABLE ONLY action_gestion_operation
    ADD CONSTRAINT action_comment_operation_pkey PRIMARY KEY (ago_id);
ALTER TABLE ONLY action_detail
    ADD CONSTRAINT action_detail_pkey PRIMARY KEY (ad_id);
ALTER TABLE ONLY action_gestion_comment
    ADD CONSTRAINT action_gestion_comment_pkey PRIMARY KEY (agc_id);
ALTER TABLE ONLY action_gestion
    ADD CONSTRAINT action_gestion_pkey PRIMARY KEY (ag_id);
ALTER TABLE ONLY action_gestion_related
    ADD CONSTRAINT action_gestion_related_pkey PRIMARY KEY (aga_id);
ALTER TABLE ONLY action_person
    ADD CONSTRAINT action_person_pkey PRIMARY KEY (ap_id);
ALTER TABLE ONLY action
    ADD CONSTRAINT action_pkey PRIMARY KEY (ac_id);
ALTER TABLE ONLY action_tags
    ADD CONSTRAINT action_tags_pkey PRIMARY KEY (at_id);
ALTER TABLE ONLY attr_def
    ADD CONSTRAINT attr_def_pkey PRIMARY KEY (ad_id);
ALTER TABLE ONLY bilan
    ADD CONSTRAINT bilan_b_name_key UNIQUE (b_name);
ALTER TABLE ONLY bilan
    ADD CONSTRAINT bilan_pkey PRIMARY KEY (b_id);
ALTER TABLE ONLY bookmark
    ADD CONSTRAINT bookmark_pkey PRIMARY KEY (b_id);
ALTER TABLE ONLY centralized
    ADD CONSTRAINT centralized_pkey PRIMARY KEY (c_id);
ALTER TABLE ONLY del_action
    ADD CONSTRAINT del_action_pkey PRIMARY KEY (del_id);
ALTER TABLE ONLY del_jrn
    ADD CONSTRAINT dj_id PRIMARY KEY (dj_id);
ALTER TABLE ONLY del_jrnx
    ADD CONSTRAINT djx_id PRIMARY KEY (djx_id);
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
ALTER TABLE ONLY formdef
    ADD CONSTRAINT formdef_pkey PRIMARY KEY (fr_id);
ALTER TABLE ONLY attr_min
    ADD CONSTRAINT frd_ad_attr_min_pk PRIMARY KEY (frd_id, ad_id);
ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT historique_analytique_pkey PRIMARY KEY (oa_id);
ALTER TABLE ONLY tmp_pcmn
    ADD CONSTRAINT id_ux UNIQUE (id);
ALTER TABLE ONLY extension
    ADD CONSTRAINT idx_ex_code UNIQUE (ex_code);
ALTER TABLE ONLY info_def
    ADD CONSTRAINT info_def_pkey PRIMARY KEY (id_type);
ALTER TABLE ONLY fiche_detail
    ADD CONSTRAINT jnt_fic_att_value_pkey PRIMARY KEY (jft_id);
ALTER TABLE ONLY jnt_letter
    ADD CONSTRAINT jnt_letter_pk PRIMARY KEY (jl_id);
ALTER TABLE ONLY jrn_def
    ADD CONSTRAINT jrn_def_jrn_def_name_key UNIQUE (jrn_def_name);
ALTER TABLE ONLY jrn_def
    ADD CONSTRAINT jrn_def_pkey PRIMARY KEY (jrn_def_id);
ALTER TABLE ONLY jrn_info
    ADD CONSTRAINT jrn_info_pkey PRIMARY KEY (ji_id);
ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_periode_periode_ledger UNIQUE (jrn_def_id, p_id);
ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_periode_pk PRIMARY KEY (id);
ALTER TABLE ONLY jrn
    ADD CONSTRAINT jrn_pkey PRIMARY KEY (jr_id, jr_def_id);
ALTER TABLE ONLY jrn_rapt
    ADD CONSTRAINT jrn_rapt_pkey PRIMARY KEY (jra_id);
ALTER TABLE ONLY jrn_type
    ADD CONSTRAINT jrn_type_pkey PRIMARY KEY (jrn_type_id);
ALTER TABLE ONLY jrn_note
    ADD CONSTRAINT jrnx_note_pkey PRIMARY KEY (n_id);
ALTER TABLE ONLY jrnx
    ADD CONSTRAINT jrnx_pkey PRIMARY KEY (j_id);
ALTER TABLE ONLY key_distribution_activity
    ADD CONSTRAINT key_distribution_activity_pkey PRIMARY KEY (ka_id);
ALTER TABLE ONLY key_distribution_detail
    ADD CONSTRAINT key_distribution_detail_pkey PRIMARY KEY (ke_id);
ALTER TABLE ONLY key_distribution_ledger
    ADD CONSTRAINT key_distribution_ledger_pkey PRIMARY KEY (kl_id);
ALTER TABLE ONLY key_distribution
    ADD CONSTRAINT key_distribution_pkey PRIMARY KEY (kd_id);
ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT letter_cred_j_id_key UNIQUE (j_id);
ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT letter_cred_pk PRIMARY KEY (lc_id);
ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT letter_deb_j_id_key UNIQUE (j_id);
ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT letter_deb_pk PRIMARY KEY (ld_id);
ALTER TABLE ONLY link_action_type
    ADD CONSTRAINT link_action_type_pkey PRIMARY KEY (l_id);
ALTER TABLE ONLY menu_default
    ADD CONSTRAINT menu_default_md_code_key UNIQUE (md_code);
ALTER TABLE ONLY menu_default
    ADD CONSTRAINT menu_default_pkey PRIMARY KEY (md_id);
ALTER TABLE ONLY menu_ref
    ADD CONSTRAINT menu_ref_pkey PRIMARY KEY (me_code);
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
ALTER TABLE ONLY profile_menu
    ADD CONSTRAINT profile_menu_pkey PRIMARY KEY (pm_id);
ALTER TABLE ONLY profile_menu_type
    ADD CONSTRAINT profile_menu_type_pkey PRIMARY KEY (pm_type);
ALTER TABLE ONLY profile
    ADD CONSTRAINT profile_pkey PRIMARY KEY (p_id);
ALTER TABLE ONLY profile_sec_repository
    ADD CONSTRAINT profile_sec_repository_pkey PRIMARY KEY (ur_id);
ALTER TABLE ONLY profile_sec_repository
    ADD CONSTRAINT profile_sec_repository_r_id_p_id_u UNIQUE (r_id, p_id);
ALTER TABLE ONLY profile_user
    ADD CONSTRAINT profile_user_pkey PRIMARY KEY (pu_id);
ALTER TABLE ONLY profile_user
    ADD CONSTRAINT profile_user_user_name_key UNIQUE (user_name, p_id);
ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT qp_id_pk PRIMARY KEY (qp_id);
ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT qs_id_pk PRIMARY KEY (qs_id);
ALTER TABLE ONLY quant_fin
    ADD CONSTRAINT quant_fin_pk PRIMARY KEY (qf_id);
ALTER TABLE ONLY stock_change
    ADD CONSTRAINT stock_change_pkey PRIMARY KEY (c_id);
ALTER TABLE ONLY stock_goods
    ADD CONSTRAINT stock_goods_pkey PRIMARY KEY (sg_id);
ALTER TABLE ONLY stock_repository
    ADD CONSTRAINT stock_repository_pkey PRIMARY KEY (r_id);
ALTER TABLE ONLY tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (t_id);
ALTER TABLE ONLY tmp_pcmn
    ADD CONSTRAINT tmp_pcmn_pkey PRIMARY KEY (pcm_val);
ALTER TABLE ONLY tmp_stockgood_detail
    ADD CONSTRAINT tmp_stockgood_detail_pkey PRIMARY KEY (d_id);
ALTER TABLE ONLY tmp_stockgood
    ADD CONSTRAINT tmp_stockgood_pkey PRIMARY KEY (s_id);
ALTER TABLE ONLY todo_list
    ADD CONSTRAINT todo_list_pkey PRIMARY KEY (tl_id);
ALTER TABLE ONLY todo_list_shared
    ADD CONSTRAINT todo_list_shared_pkey PRIMARY KEY (id);
ALTER TABLE ONLY tool_uos
    ADD CONSTRAINT tool_uos_pkey PRIMARY KEY (uos_value);
ALTER TABLE ONLY tva_rate
    ADD CONSTRAINT tva_id_pk PRIMARY KEY (tva_id);
ALTER TABLE ONLY user_sec_jrn
    ADD CONSTRAINT uniq_user_ledger UNIQUE (uj_login, uj_jrn_id);
ALTER TABLE ONLY todo_list_shared
    ADD CONSTRAINT unique_todo_list_id_login UNIQUE (todo_list_id, use_login);
ALTER TABLE ONLY user_active_security
    ADD CONSTRAINT user_active_security_pk PRIMARY KEY (id);
ALTER TABLE ONLY user_filter
    ADD CONSTRAINT user_filter_pkey PRIMARY KEY (id);
ALTER TABLE ONLY user_sec_act
    ADD CONSTRAINT user_sec_act_pkey PRIMARY KEY (ua_id);
ALTER TABLE ONLY user_sec_action_profile
    ADD CONSTRAINT user_sec_action_profile_p_id_p_granted_u UNIQUE (p_id, p_granted);
ALTER TABLE ONLY user_sec_action_profile
    ADD CONSTRAINT user_sec_action_profile_pkey PRIMARY KEY (ua_id);
ALTER TABLE ONLY user_sec_jrn
    ADD CONSTRAINT user_sec_jrn_pkey PRIMARY KEY (uj_id);
ALTER TABLE ONLY action_gestion_related
    ADD CONSTRAINT ux_aga_least_aga_greatest UNIQUE (aga_least, aga_greatest);
ALTER TABLE ONLY jrn
    ADD CONSTRAINT ux_internal UNIQUE (jr_internal);
ALTER TABLE ONLY version
    ADD CONSTRAINT version_pkey PRIMARY KEY (val);
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
ALTER TABLE ONLY fiche_detail
    ADD CONSTRAINT "$1" FOREIGN KEY (f_id) REFERENCES fiche(f_id);
ALTER TABLE ONLY jnt_fic_attr
    ADD CONSTRAINT "$1" FOREIGN KEY (fd_id) REFERENCES fiche_def(fd_id);
ALTER TABLE ONLY jrn
    ADD CONSTRAINT "$1" FOREIGN KEY (jr_def_id) REFERENCES jrn_def(jrn_def_id);
ALTER TABLE ONLY jrn_def
    ADD CONSTRAINT "$1" FOREIGN KEY (jrn_def_type) REFERENCES jrn_type(jrn_type_id);
ALTER TABLE ONLY jrnx
    ADD CONSTRAINT "$2" FOREIGN KEY (j_jrn_def) REFERENCES jrn_def(jrn_def_id);
ALTER TABLE ONLY attr_min
    ADD CONSTRAINT "$2" FOREIGN KEY (ad_id) REFERENCES attr_def(ad_id);
ALTER TABLE ONLY action_gestion_operation
    ADD CONSTRAINT action_comment_operation_ag_id_fkey FOREIGN KEY (ag_id) REFERENCES action_gestion(ag_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_gestion_operation
    ADD CONSTRAINT action_comment_operation_jr_id_fkey FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_detail
    ADD CONSTRAINT action_detail_ag_id_fkey FOREIGN KEY (ag_id) REFERENCES action_gestion(ag_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_person
    ADD CONSTRAINT action_gestion_ag_id_fk2 FOREIGN KEY (ag_id) REFERENCES action_gestion(ag_id);
ALTER TABLE ONLY action_gestion_comment
    ADD CONSTRAINT action_gestion_comment_ag_id_fkey FOREIGN KEY (ag_id) REFERENCES action_gestion(ag_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_gestion_related
    ADD CONSTRAINT action_gestion_related_aga_greatest_fkey FOREIGN KEY (aga_greatest) REFERENCES action_gestion(ag_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_gestion_related
    ADD CONSTRAINT action_gestion_related_aga_least_fkey FOREIGN KEY (aga_least) REFERENCES action_gestion(ag_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_gestion_related
    ADD CONSTRAINT action_gestion_related_aga_type_fkey FOREIGN KEY (aga_type) REFERENCES link_action_type(l_id);
ALTER TABLE ONLY action_person
    ADD CONSTRAINT action_person_ag_id_fkey FOREIGN KEY (ag_id) REFERENCES action_gestion(ag_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_person
    ADD CONSTRAINT action_person_f_id_fkey FOREIGN KEY (f_id) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_tags
    ADD CONSTRAINT action_tags_ag_id_fkey FOREIGN KEY (ag_id) REFERENCES action_gestion(ag_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_tags
    ADD CONSTRAINT action_tags_t_id_fkey FOREIGN KEY (t_id) REFERENCES tags(t_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY forecast_item
    ADD CONSTRAINT card FOREIGN KEY (fi_card) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY fiche_detail
    ADD CONSTRAINT fiche_detail_attr_def_fk FOREIGN KEY (ad_id) REFERENCES attr_def(ad_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_person
    ADD CONSTRAINT fiche_f_id_fk2 FOREIGN KEY (f_id) REFERENCES fiche(f_id);
ALTER TABLE ONLY action_gestion
    ADD CONSTRAINT fiche_f_id_fk3 FOREIGN KEY (f_id_dest) REFERENCES fiche(f_id);
ALTER TABLE ONLY action_gestion
    ADD CONSTRAINT fk_action_gestion_document_type FOREIGN KEY (ag_type) REFERENCES document_type(dt_id);
ALTER TABLE ONLY quant_fin
    ADD CONSTRAINT fk_card FOREIGN KEY (qf_bank) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY quant_fin
    ADD CONSTRAINT fk_card_other FOREIGN KEY (qf_other) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY forecast_item
    ADD CONSTRAINT fk_forecast FOREIGN KEY (fc_id) REFERENCES forecast_cat(fc_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_info
    ADD CONSTRAINT fk_info_def FOREIGN KEY (id_type) REFERENCES info_def(id_type) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_info
    ADD CONSTRAINT fk_jrn FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY quant_fin
    ADD CONSTRAINT fk_jrn FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY groupe_analytique
    ADD CONSTRAINT fk_pa_id FOREIGN KEY (pa_id) REFERENCES plan_analytique(pa_id) ON DELETE CASCADE;
ALTER TABLE ONLY jrnx
    ADD CONSTRAINT fk_pcmn_val FOREIGN KEY (j_poste) REFERENCES tmp_pcmn(pcm_val);
ALTER TABLE ONLY centralized
    ADD CONSTRAINT fk_pcmn_val FOREIGN KEY (c_poste) REFERENCES tmp_pcmn(pcm_val);
ALTER TABLE ONLY stock_goods
    ADD CONSTRAINT fk_stock_good_f_id FOREIGN KEY (f_id) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY todo_list_shared
    ADD CONSTRAINT fk_todo_list_shared_todo_list FOREIGN KEY (todo_list_id) REFERENCES todo_list(tl_id);
ALTER TABLE ONLY forecast_cat
    ADD CONSTRAINT forecast_child FOREIGN KEY (f_id) REFERENCES forecast(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY forecast
    ADD CONSTRAINT forecast_f_end_date_fkey FOREIGN KEY (f_end_date) REFERENCES parm_periode(p_id) ON UPDATE SET NULL ON DELETE SET NULL;
ALTER TABLE ONLY forecast
    ADD CONSTRAINT forecast_f_start_date_fkey FOREIGN KEY (f_start_date) REFERENCES parm_periode(p_id) ON UPDATE SET NULL ON DELETE SET NULL;
ALTER TABLE ONLY form
    ADD CONSTRAINT formdef_fk FOREIGN KEY (fo_fr_id) REFERENCES formdef(fr_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT jnt_cred_fk FOREIGN KEY (jl_id) REFERENCES jnt_letter(jl_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT jnt_deb_fk FOREIGN KEY (jl_id) REFERENCES jnt_letter(jl_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jnt_fic_attr
    ADD CONSTRAINT jnt_fic_attr_attr_def_fk FOREIGN KEY (ad_id) REFERENCES attr_def(ad_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY op_predef
    ADD CONSTRAINT jrn_def_id_fk FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_per_jrn_def_id FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_periode_p_id FOREIGN KEY (p_id) REFERENCES parm_periode(p_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_rapt
    ADD CONSTRAINT jrn_rapt_jr_id_fkey FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrn_rapt
    ADD CONSTRAINT jrn_rapt_jra_concerned_fkey FOREIGN KEY (jra_concerned) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jrnx
    ADD CONSTRAINT jrnx_f_id_fkey FOREIGN KEY (f_id) REFERENCES fiche(f_id) ON UPDATE CASCADE;
ALTER TABLE ONLY jrn_note
    ADD CONSTRAINT jrnx_note_j_id_fkey FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY key_distribution_activity
    ADD CONSTRAINT key_distribution_activity_ke_id_fkey FOREIGN KEY (ke_id) REFERENCES key_distribution_detail(ke_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY key_distribution_activity
    ADD CONSTRAINT key_distribution_activity_pa_id_fkey FOREIGN KEY (pa_id) REFERENCES plan_analytique(pa_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY key_distribution_activity
    ADD CONSTRAINT key_distribution_activity_po_id_fkey FOREIGN KEY (po_id) REFERENCES poste_analytique(po_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY key_distribution_detail
    ADD CONSTRAINT key_distribution_detail_kd_id_fkey FOREIGN KEY (kd_id) REFERENCES key_distribution(kd_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY key_distribution_ledger
    ADD CONSTRAINT key_distribution_ledger_jrn_def_id_fkey FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY key_distribution_ledger
    ADD CONSTRAINT key_distribution_ledger_kd_id_fkey FOREIGN KEY (kd_id) REFERENCES key_distribution(kd_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT letter_cred_fk FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT letter_deb_fk FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY document_modele
    ADD CONSTRAINT md_type FOREIGN KEY (md_type) REFERENCES document_type(dt_id);
ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_jrn_def_id_fk FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_mp_fd_id_fkey FOREIGN KEY (mp_fd_id) REFERENCES fiche_def(fd_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_mp_jrn_def_id_fkey FOREIGN KEY (mp_jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT operation_analytique_fiche_id_fk FOREIGN KEY (f_id) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT operation_analytique_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT operation_analytique_po_id_fkey FOREIGN KEY (po_id) REFERENCES poste_analytique(po_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY poste_analytique
    ADD CONSTRAINT poste_analytique_pa_id_fkey FOREIGN KEY (pa_id) REFERENCES plan_analytique(pa_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY action_gestion
    ADD CONSTRAINT profile_fkey FOREIGN KEY (ag_dest) REFERENCES profile(p_id) ON UPDATE SET NULL ON DELETE SET NULL;
ALTER TABLE ONLY profile_menu
    ADD CONSTRAINT profile_menu_me_code_fkey FOREIGN KEY (me_code) REFERENCES menu_ref(me_code) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY profile_menu
    ADD CONSTRAINT profile_menu_p_id_fkey FOREIGN KEY (p_id) REFERENCES profile(p_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY profile_menu
    ADD CONSTRAINT profile_menu_type_fkey FOREIGN KEY (p_type_display) REFERENCES profile_menu_type(pm_type);
ALTER TABLE ONLY profile_sec_repository
    ADD CONSTRAINT profile_sec_repository_p_id_fkey FOREIGN KEY (p_id) REFERENCES profile(p_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY profile_sec_repository
    ADD CONSTRAINT profile_sec_repository_r_id_fkey FOREIGN KEY (r_id) REFERENCES stock_repository(r_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY profile_user
    ADD CONSTRAINT profile_user_p_id_fkey FOREIGN KEY (p_id) REFERENCES profile(p_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT qp_vat_code_fk FOREIGN KEY (qp_vat_code) REFERENCES tva_rate(tva_id);
ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT qs_vat_code_fk FOREIGN KEY (qs_vat_code) REFERENCES tva_rate(tva_id);
ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT quant_purchase_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT quant_purchase_qp_internal_fkey FOREIGN KEY (qp_internal) REFERENCES jrn(jr_internal) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;
ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT quant_sold_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT quant_sold_qs_internal_fkey FOREIGN KEY (qs_internal) REFERENCES jrn(jr_internal) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;
ALTER TABLE ONLY stock_change
    ADD CONSTRAINT stock_change_r_id_fkey FOREIGN KEY (r_id) REFERENCES stock_repository(r_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY stock_goods
    ADD CONSTRAINT stock_goods_c_id_fkey FOREIGN KEY (c_id) REFERENCES stock_change(c_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY stock_goods
    ADD CONSTRAINT stock_goods_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY tmp_stockgood_detail
    ADD CONSTRAINT tmp_stockgood_detail_s_id_fkey FOREIGN KEY (s_id) REFERENCES tmp_stockgood(s_id) ON DELETE CASCADE;
ALTER TABLE ONLY user_sec_jrn
    ADD CONSTRAINT uj_priv_id_fkey FOREIGN KEY (uj_jrn_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY user_sec_action_profile
    ADD CONSTRAINT user_sec_action_profile_p_granted_fkey FOREIGN KEY (p_granted) REFERENCES profile(p_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY user_sec_action_profile
    ADD CONSTRAINT user_sec_action_profile_p_id_fkey FOREIGN KEY (p_id) REFERENCES profile(p_id) ON UPDATE CASCADE ON DELETE CASCADE;
CREATE TRIGGER action_gestion_t_insert_update BEFORE INSERT OR UPDATE ON action_gestion FOR EACH ROW EXECUTE PROCEDURE comptaproc.action_gestion_ins_upd();
CREATE TRIGGER document_modele_validate BEFORE INSERT OR UPDATE ON document_modele FOR EACH ROW EXECUTE PROCEDURE comptaproc.t_document_modele_validate();
CREATE TRIGGER document_validate BEFORE INSERT OR UPDATE ON document FOR EACH ROW EXECUTE PROCEDURE comptaproc.t_document_validate();
CREATE TRIGGER fiche_def_ins_upd BEFORE INSERT OR UPDATE ON fiche_def FOR EACH ROW EXECUTE PROCEDURE comptaproc.fiche_def_ins_upd();
CREATE TRIGGER fiche_detail_upd_trg AFTER UPDATE ON fiche_detail FOR EACH ROW EXECUTE PROCEDURE comptaproc.fiche_detail_qcode_upd();
CREATE TRIGGER info_def_ins_upd_t BEFORE INSERT OR UPDATE ON info_def FOR EACH ROW EXECUTE PROCEDURE comptaproc.info_def_ins_upd();
CREATE TRIGGER jrn_def_description_ins_upd BEFORE INSERT OR UPDATE ON jrn_def FOR EACH ROW EXECUTE PROCEDURE comptaproc.t_jrn_def_description();
CREATE TRIGGER opd_limit_description BEFORE INSERT OR UPDATE ON op_predef FOR EACH ROW EXECUTE PROCEDURE comptaproc.opd_limit_description();
CREATE TRIGGER parm_periode_check_periode_trg BEFORE INSERT OR UPDATE ON parm_periode FOR EACH ROW EXECUTE PROCEDURE comptaproc.check_periode();
CREATE TRIGGER profile_user_ins_upd BEFORE INSERT OR UPDATE ON profile_user FOR EACH ROW EXECUTE PROCEDURE comptaproc.trg_profile_user_ins_upd();
CREATE TRIGGER quant_sold_ins_upd_tr AFTER INSERT OR UPDATE ON quant_purchase FOR EACH ROW EXECUTE PROCEDURE comptaproc.quant_purchase_ins_upd();
CREATE TRIGGER quant_sold_ins_upd_tr AFTER INSERT OR UPDATE ON quant_sold FOR EACH ROW EXECUTE PROCEDURE comptaproc.quant_sold_ins_upd();
CREATE TRIGGER remove_action_gestion AFTER DELETE ON fiche FOR EACH ROW EXECUTE PROCEDURE comptaproc.card_after_delete();
CREATE TRIGGER t_check_balance AFTER INSERT OR UPDATE ON jrn FOR EACH ROW EXECUTE PROCEDURE comptaproc.proc_check_balance();
CREATE TRIGGER t_check_jrn BEFORE INSERT OR DELETE OR UPDATE ON jrn FOR EACH ROW EXECUTE PROCEDURE comptaproc.jrn_check_periode();
CREATE TRIGGER t_group_analytic_del BEFORE DELETE ON groupe_analytique FOR EACH ROW EXECUTE PROCEDURE comptaproc.group_analytique_del();
CREATE TRIGGER t_group_analytic_ins_upd BEFORE INSERT OR UPDATE ON groupe_analytique FOR EACH ROW EXECUTE PROCEDURE comptaproc.group_analytic_ins_upd();
CREATE TRIGGER t_jnt_fic_attr_ins AFTER INSERT ON jnt_fic_attr FOR EACH ROW EXECUTE PROCEDURE comptaproc.jnt_fic_attr_ins();
CREATE TRIGGER t_jrn_def_add_periode AFTER INSERT ON jrn_def FOR EACH ROW EXECUTE PROCEDURE comptaproc.jrn_def_add();
CREATE TRIGGER t_jrn_def_delete BEFORE DELETE ON jrn_def FOR EACH ROW EXECUTE PROCEDURE comptaproc.jrn_def_delete();
CREATE TRIGGER t_jrn_del BEFORE DELETE ON jrn FOR EACH ROW EXECUTE PROCEDURE comptaproc.jrn_del();
CREATE TRIGGER t_jrnx_del BEFORE DELETE ON jrnx FOR EACH ROW EXECUTE PROCEDURE comptaproc.jrnx_del();
CREATE TRIGGER t_jrnx_ins BEFORE INSERT ON jrnx FOR EACH ROW EXECUTE PROCEDURE comptaproc.jrnx_ins();
CREATE TRIGGER t_jrnx_upd BEFORE UPDATE ON jrnx FOR EACH ROW EXECUTE PROCEDURE comptaproc.jrnx_ins();
CREATE TRIGGER t_letter_del AFTER DELETE ON jrnx FOR EACH ROW EXECUTE PROCEDURE comptaproc.jrnx_letter_del();
CREATE TRIGGER t_plan_analytique_ins_upd BEFORE INSERT OR UPDATE ON plan_analytique FOR EACH ROW EXECUTE PROCEDURE comptaproc.plan_analytic_ins_upd();
CREATE TRIGGER t_poste_analytique_ins_upd BEFORE INSERT OR UPDATE ON poste_analytique FOR EACH ROW EXECUTE PROCEDURE comptaproc.poste_analytique_ins_upd();
CREATE TRIGGER t_tmp_pcm_alphanum_ins_upd BEFORE INSERT OR UPDATE ON tmp_pcmn FOR EACH ROW EXECUTE PROCEDURE comptaproc.tmp_pcmn_alphanum_ins_upd();
CREATE TRIGGER t_tmp_pcmn_ins BEFORE INSERT ON tmp_pcmn FOR EACH ROW EXECUTE PROCEDURE comptaproc.tmp_pcmn_ins();
CREATE TRIGGER todo_list_ins_upd BEFORE INSERT OR UPDATE ON todo_list FOR EACH ROW EXECUTE PROCEDURE comptaproc.trg_todo_list_ins_upd();
CREATE TRIGGER todo_list_shared_ins_upd BEFORE INSERT OR UPDATE ON todo_list_shared FOR EACH ROW EXECUTE PROCEDURE comptaproc.trg_todo_list_shared_ins_upd();
CREATE TRIGGER trg_action_gestion_related BEFORE INSERT OR UPDATE ON action_gestion_related FOR EACH ROW EXECUTE PROCEDURE comptaproc.action_gestion_related_ins_up();
CREATE TRIGGER trg_category_card_before_delete BEFORE DELETE ON fiche_def FOR EACH ROW EXECUTE PROCEDURE comptaproc.category_card_before_delete();
CREATE TRIGGER trg_extension_ins_upd BEFORE INSERT OR UPDATE ON extension FOR EACH ROW EXECUTE PROCEDURE comptaproc.extension_ins_upd();
CREATE TRIGGER trigger_document_type_i AFTER INSERT ON document_type FOR EACH ROW EXECUTE PROCEDURE comptaproc.t_document_type_insert();
CREATE TRIGGER trigger_jrn_def_sequence_i AFTER INSERT ON jrn_def FOR EACH ROW EXECUTE PROCEDURE comptaproc.t_jrn_def_sequence();
CREATE TRIGGER user_sec_act_ins_upd BEFORE INSERT OR UPDATE ON user_sec_act FOR EACH ROW EXECUTE PROCEDURE comptaproc.trg_user_sec_act_ins_upd();
CREATE TRIGGER user_sec_jrn_after_ins_upd BEFORE INSERT OR UPDATE ON user_sec_jrn FOR EACH ROW EXECUTE PROCEDURE comptaproc.trg_user_sec_jrn_ins_upd();
COMMENT ON CONSTRAINT uniq_user_ledger ON user_sec_jrn IS 'Create an unique combination user / ledger';
COMMENT ON TRIGGER action_gestion_t_insert_update ON action_gestion IS 'Truncate the column ag_title to 70 char';
COMMENT ON TRIGGER profile_user_ins_upd ON profile_user IS 'Force the column user_name to lowercase';
COMMENT ON TRIGGER t_jrnx_ins ON jrnx IS 'check that the qcode used by the card exists and format it : uppercase and trim the space';
COMMENT ON TRIGGER t_letter_del ON jrnx IS 'Delete the lettering for this row';
COMMENT ON TRIGGER todo_list_ins_upd ON todo_list IS 'Force the column use_login to lowercase';
COMMENT ON TRIGGER todo_list_shared_ins_upd ON todo_list_shared IS 'Force the column ua_login to lowercase';
COMMENT ON TRIGGER user_sec_act_ins_upd ON user_sec_act IS 'Force the column ua_login to lowercase';
COMMENT ON TRIGGER user_sec_jrn_after_ins_upd ON user_sec_jrn IS 'Force the column uj_login to lowercase';
