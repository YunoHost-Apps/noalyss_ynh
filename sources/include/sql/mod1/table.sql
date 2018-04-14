CREATE DOMAIN account_type AS character varying(40);
CREATE TABLE action (
    ac_id integer NOT NULL,
    ac_description text NOT NULL,
    ac_module text,
    ac_code character varying(30)
);
CREATE TABLE action_detail (
    ad_id integer NOT NULL,
    f_id bigint,
    ad_text text,
    ad_pu numeric(20,4) DEFAULT 0,
    ad_quant numeric(20,4) DEFAULT 0,
    ad_tva_id integer DEFAULT 0,
    ad_tva_amount numeric(20,4) DEFAULT 0,
    ad_total_amount numeric(20,4) DEFAULT 0,
    ag_id integer DEFAULT 0 NOT NULL
);
CREATE TABLE action_gestion (
    ag_id integer DEFAULT nextval('action_gestion_ag_id_seq'::regclass) NOT NULL,
    ag_type integer,
    f_id_dest integer,
    ag_title text,
    ag_timestamp timestamp without time zone DEFAULT now(),
    ag_ref text,
    ag_hour text,
    ag_priority integer DEFAULT 2,
    ag_dest bigint DEFAULT (-1) NOT NULL,
    ag_owner text,
    ag_contact bigint,
    ag_state integer,
    ag_remind_date date
);
CREATE TABLE action_gestion_comment (
    agc_id bigint NOT NULL,
    ag_id bigint,
    agc_date timestamp with time zone DEFAULT now(),
    agc_comment text,
    tech_user text
);
CREATE TABLE action_gestion_operation (
    ago_id bigint NOT NULL,
    ag_id bigint,
    jr_id bigint
);
CREATE TABLE action_gestion_related (
    aga_id bigint NOT NULL,
    aga_least bigint NOT NULL,
    aga_greatest bigint NOT NULL,
    aga_type bigint
);
CREATE TABLE action_person (
    ap_id integer NOT NULL,
    ag_id integer NOT NULL,
    f_id integer NOT NULL
);
CREATE TABLE action_tags (
    at_id integer NOT NULL,
    t_id integer,
    ag_id integer
);
CREATE TABLE attr_def (
    ad_id integer DEFAULT nextval(('s_attr_def'::text)::regclass) NOT NULL,
    ad_text text,
    ad_type text,
    ad_size text NOT NULL,
    ad_extra text
);
CREATE TABLE attr_min (
    frd_id integer NOT NULL,
    ad_id integer NOT NULL
);
CREATE TABLE bilan (
    b_id integer DEFAULT nextval('bilan_b_id_seq'::regclass) NOT NULL,
    b_name text NOT NULL,
    b_file_template text NOT NULL,
    b_file_form text,
    b_type text NOT NULL
);
CREATE TABLE bookmark (
    b_id integer NOT NULL,
    b_order integer DEFAULT 1,
    b_action text,
    login text
);
CREATE TABLE centralized (
    c_id integer DEFAULT nextval(('s_centralized'::text)::regclass) NOT NULL,
    c_j_id integer,
    c_date date NOT NULL,
    c_internal text NOT NULL,
    c_montant numeric(20,4) NOT NULL,
    c_debit boolean DEFAULT true,
    c_jrn_def integer NOT NULL,
    c_poste account_type,
    c_description text,
    c_grp integer NOT NULL,
    c_comment text,
    c_rapt text,
    c_periode integer,
    c_order integer
);
CREATE TABLE del_action (
    del_id integer NOT NULL,
    del_name text NOT NULL,
    del_time timestamp without time zone
);
CREATE TABLE del_jrn (
    jr_id integer NOT NULL,
    jr_def_id integer,
    jr_montant numeric(20,4),
    jr_comment text,
    jr_date date,
    jr_grpt_id integer,
    jr_internal text,
    jr_tech_date timestamp without time zone,
    jr_tech_per integer,
    jrn_ech date,
    jr_ech date,
    jr_rapt text,
    jr_valid boolean,
    jr_opid integer,
    jr_c_opid integer,
    jr_pj oid,
    jr_pj_name text,
    jr_pj_type text,
    del_jrn_date timestamp without time zone,
    jr_pj_number text,
    dj_id integer NOT NULL
);
CREATE TABLE del_jrnx (
    j_id integer NOT NULL,
    j_date date,
    j_montant numeric(20,4),
    j_poste account_type,
    j_grpt integer,
    j_rapt text,
    j_jrn_def integer,
    j_debit boolean,
    j_text text,
    j_centralized boolean,
    j_internal text,
    j_tech_user text,
    j_tech_date timestamp without time zone,
    j_tech_per integer,
    j_qcode text,
    djx_id integer NOT NULL,
    f_id bigint
);
CREATE TABLE document (
    d_id integer DEFAULT nextval('document_d_id_seq'::regclass) NOT NULL,
    ag_id integer NOT NULL,
    d_lob oid,
    d_number bigint NOT NULL,
    d_filename text,
    d_mimetype text,
    d_description text
);
CREATE TABLE document_modele (
    md_id integer DEFAULT nextval('document_modele_md_id_seq'::regclass) NOT NULL,
    md_name text NOT NULL,
    md_lob oid,
    md_type integer NOT NULL,
    md_filename text,
    md_mimetype text,
    md_affect character varying(3) NOT NULL
);
CREATE TABLE document_state (
    s_id integer DEFAULT nextval('document_state_s_id_seq'::regclass) NOT NULL,
    s_value character varying(50) NOT NULL,
    s_status character(1)
);
CREATE TABLE document_type (
    dt_id integer DEFAULT nextval('document_type_dt_id_seq'::regclass) NOT NULL,
    dt_value character varying(80),
    dt_prefix text
);
CREATE TABLE extension (
    ex_id integer NOT NULL,
    ex_name character varying(30) NOT NULL,
    ex_code character varying(15) NOT NULL,
    ex_desc character varying(250),
    ex_file character varying NOT NULL,
    ex_enable "char" DEFAULT 'Y'::"char" NOT NULL
);
CREATE TABLE fiche (
    f_id integer DEFAULT nextval(('s_fiche'::text)::regclass) NOT NULL,
    fd_id integer
);
CREATE TABLE fiche_def (
    fd_id integer DEFAULT nextval(('s_fdef'::text)::regclass) NOT NULL,
    fd_class_base text,
    fd_label text NOT NULL,
    fd_create_account boolean DEFAULT false,
    frd_id integer NOT NULL,
    fd_description text
);
CREATE TABLE fiche_def_ref (
    frd_id integer DEFAULT nextval(('s_fiche_def_ref'::text)::regclass) NOT NULL,
    frd_text text,
    frd_class_base account_type
);
CREATE TABLE fiche_detail (
    jft_id integer DEFAULT nextval(('s_jnt_fic_att_value'::text)::regclass) NOT NULL,
    f_id integer,
    ad_id integer,
    ad_value text
);
CREATE TABLE forecast (
    f_id integer NOT NULL,
    f_name text NOT NULL,
    f_start_date bigint,
    f_end_date bigint
);
CREATE TABLE forecast_cat (
    fc_id integer NOT NULL,
    fc_desc text NOT NULL,
    f_id bigint,
    fc_order integer DEFAULT 0 NOT NULL
);
CREATE TABLE forecast_item (
    fi_id integer NOT NULL,
    fi_text text,
    fi_account text,
    fi_card integer,
    fi_order integer,
    fc_id integer,
    fi_amount numeric(20,4) DEFAULT 0,
    fi_debit "char" DEFAULT 'd'::"char" NOT NULL,
    fi_pid integer
);
CREATE TABLE form (
    fo_id integer DEFAULT nextval(('s_form'::text)::regclass) NOT NULL,
    fo_fr_id integer,
    fo_pos integer,
    fo_label text,
    fo_formula text
);
CREATE TABLE formdef (
    fr_id integer DEFAULT nextval(('s_formdef'::text)::regclass) NOT NULL,
    fr_label text
);
CREATE TABLE groupe_analytique (
    ga_id character varying(10) NOT NULL,
    pa_id integer,
    ga_description text
);
CREATE TABLE info_def (
    id_type text NOT NULL,
    id_description text
);
CREATE TABLE jnt_fic_attr (
    fd_id integer,
    ad_id integer,
    jnt_id bigint DEFAULT nextval('s_jnt_id'::regclass) NOT NULL,
    jnt_order integer NOT NULL
);
CREATE TABLE jnt_letter (
    jl_id integer NOT NULL
);
CREATE TABLE jrn (
    jr_id integer DEFAULT nextval(('s_jrn'::text)::regclass) NOT NULL,
    jr_def_id integer NOT NULL,
    jr_montant numeric(20,4) NOT NULL,
    jr_comment text,
    jr_date date,
    jr_grpt_id integer NOT NULL,
    jr_internal text,
    jr_tech_date timestamp without time zone DEFAULT now() NOT NULL,
    jr_tech_per integer NOT NULL,
    jrn_ech date,
    jr_ech date,
    jr_rapt text,
    jr_valid boolean DEFAULT true,
    jr_opid integer,
    jr_c_opid integer,
    jr_pj oid,
    jr_pj_name text,
    jr_pj_type text,
    jr_pj_number text,
    jr_mt text,
    jr_date_paid date,
    jr_optype character varying(3) DEFAULT 'NOR'::character varying
);
CREATE TABLE jrn_def (
    jrn_def_id integer DEFAULT nextval(('s_jrn_def'::text)::regclass) NOT NULL,
    jrn_def_name text NOT NULL,
    jrn_def_class_deb text,
    jrn_def_class_cred text,
    jrn_def_fiche_deb text,
    jrn_def_fiche_cred text,
    jrn_deb_max_line integer DEFAULT 1,
    jrn_cred_max_line integer DEFAULT 1,
    jrn_def_ech boolean DEFAULT false,
    jrn_def_ech_lib text,
    jrn_def_type character(3) NOT NULL,
    jrn_def_code text NOT NULL,
    jrn_def_pj_pref text,
    jrn_def_bank bigint,
    jrn_def_num_op integer,
    jrn_def_description text,
    jrn_enable integer DEFAULT 1
);
CREATE TABLE jrn_info (
    ji_id integer NOT NULL,
    jr_id integer NOT NULL,
    id_type text NOT NULL,
    ji_value text
);
CREATE TABLE jrn_note (
    n_id integer NOT NULL,
    n_text text,
    jr_id bigint NOT NULL
);
CREATE TABLE jrn_periode (
    jrn_def_id integer NOT NULL,
    p_id integer NOT NULL,
    status text,
    id bigint DEFAULT nextval('jrn_periode_id_seq'::regclass) NOT NULL
);
CREATE TABLE jrn_rapt (
    jra_id integer DEFAULT nextval(('s_jrn_rapt'::text)::regclass) NOT NULL,
    jr_id integer NOT NULL,
    jra_concerned integer NOT NULL
);
CREATE TABLE jrn_type (
    jrn_type_id character(3) NOT NULL,
    jrn_desc text
);
CREATE TABLE jrnx (
    j_id integer DEFAULT nextval(('s_jrn_op'::text)::regclass) NOT NULL,
    j_date date DEFAULT now(),
    j_montant numeric(20,4) DEFAULT 0,
    j_poste account_type NOT NULL,
    j_grpt integer NOT NULL,
    j_rapt text,
    j_jrn_def integer NOT NULL,
    j_debit boolean DEFAULT true,
    j_text text,
    j_centralized boolean DEFAULT false,
    j_internal text,
    j_tech_user text NOT NULL,
    j_tech_date timestamp without time zone DEFAULT now() NOT NULL,
    j_tech_per integer NOT NULL,
    j_qcode text,
    f_id bigint
);
CREATE TABLE key_distribution (
    kd_id integer NOT NULL,
    kd_name text,
    kd_description text
);
CREATE TABLE key_distribution_activity (
    ka_id integer NOT NULL,
    ke_id bigint NOT NULL,
    po_id bigint,
    pa_id bigint NOT NULL
);
CREATE TABLE key_distribution_detail (
    ke_id integer NOT NULL,
    kd_id bigint NOT NULL,
    ke_row integer NOT NULL,
    ke_percent numeric(20,4) NOT NULL
);
CREATE TABLE key_distribution_ledger (
    kl_id integer NOT NULL,
    kd_id bigint NOT NULL,
    jrn_def_id bigint NOT NULL
);
CREATE TABLE letter_cred (
    lc_id integer NOT NULL,
    j_id bigint NOT NULL,
    jl_id bigint NOT NULL
);
CREATE TABLE letter_deb (
    ld_id integer NOT NULL,
    j_id bigint NOT NULL,
    jl_id bigint NOT NULL
);
CREATE TABLE link_action_type (
    l_id bigint NOT NULL,
    l_desc character varying
);
CREATE TABLE menu_default (
    md_id integer NOT NULL,
    md_code text NOT NULL,
    me_code text NOT NULL
);
CREATE TABLE menu_ref (
    me_code text NOT NULL,
    me_menu text,
    me_file text,
    me_url text,
    me_description text,
    me_parameter text,
    me_javascript text,
    me_type character varying(2),
    me_description_etendue text
);
CREATE TABLE mod_payment (
    mp_id integer NOT NULL,
    mp_lib text NOT NULL,
    mp_jrn_def_id integer NOT NULL,
    mp_fd_id bigint,
    mp_qcode text,
    jrn_def_id bigint
);
CREATE TABLE op_predef (
    od_id integer DEFAULT nextval('op_def_op_seq'::regclass) NOT NULL,
    jrn_def_id integer NOT NULL,
    od_name text NOT NULL,
    od_item integer NOT NULL,
    od_jrn_type text NOT NULL,
    od_direct boolean NOT NULL,
    od_description text
);
CREATE TABLE op_predef_detail (
    opd_id integer DEFAULT nextval('op_predef_detail_opd_id_seq'::regclass) NOT NULL,
    od_id integer NOT NULL,
    opd_poste text NOT NULL,
    opd_amount numeric(20,4),
    opd_tva_id integer,
    opd_quantity numeric(20,4),
    opd_debit boolean NOT NULL,
    opd_tva_amount numeric(20,4),
    opd_comment text,
    opd_qc boolean
);
CREATE TABLE operation_analytique (
    oa_id integer DEFAULT nextval('historique_analytique_ha_id_seq'::regclass) NOT NULL,
    po_id integer NOT NULL,
    oa_amount numeric(20,4) NOT NULL,
    oa_description text,
    oa_debit boolean DEFAULT true NOT NULL,
    j_id integer,
    oa_group integer DEFAULT nextval('s_oa_group'::regclass) NOT NULL,
    oa_date date NOT NULL,
    oa_row integer,
    oa_jrnx_id_source bigint,
    oa_positive character(1) DEFAULT 'Y'::bpchar NOT NULL,
    f_id bigint,
    CONSTRAINT operation_analytique_oa_amount_check CHECK ((oa_amount >= (0)::numeric))
);
CREATE TABLE parameter (
    pr_id text NOT NULL,
    pr_value text
);
CREATE TABLE parm_code (
    p_code text NOT NULL,
    p_value text,
    p_comment text
);
CREATE TABLE parm_money (
    pm_id integer DEFAULT nextval(('s_currency'::text)::regclass),
    pm_code character(3) NOT NULL,
    pm_rate numeric(20,4)
);
CREATE TABLE parm_periode (
    p_id integer DEFAULT nextval(('s_periode'::text)::regclass) NOT NULL,
    p_start date NOT NULL,
    p_end date NOT NULL,
    p_exercice text DEFAULT to_char(now(), 'YYYY'::text) NOT NULL,
    p_closed boolean DEFAULT false,
    p_central boolean DEFAULT false,
    CONSTRAINT parm_periode_check CHECK ((p_end >= p_start))
);
CREATE TABLE parm_poste (
    p_value account_type NOT NULL,
    p_type text NOT NULL
);
CREATE TABLE plan_analytique (
    pa_id integer DEFAULT nextval('plan_analytique_pa_id_seq'::regclass) NOT NULL,
    pa_name text DEFAULT 'Sans Nom'::text NOT NULL,
    pa_description text
);
CREATE TABLE poste_analytique (
    po_id integer DEFAULT nextval('poste_analytique_po_id_seq'::regclass) NOT NULL,
    po_name text NOT NULL,
    pa_id integer NOT NULL,
    po_amount numeric(20,4) DEFAULT 0.0 NOT NULL,
    po_description text,
    ga_id character varying(10)
);
CREATE TABLE profile (
    p_name text NOT NULL,
    p_id integer NOT NULL,
    p_desc text,
    with_calc boolean DEFAULT true,
    with_direct_form boolean DEFAULT true
);
CREATE TABLE profile_menu (
    pm_id integer NOT NULL,
    me_code text,
    me_code_dep text,
    p_id integer,
    p_order integer,
    p_type_display text NOT NULL,
    pm_default integer,
    pm_id_dep bigint
);
CREATE TABLE profile_menu_type (
    pm_type text NOT NULL,
    pm_desc text
);
CREATE TABLE profile_sec_repository (
    ur_id bigint NOT NULL,
    p_id bigint,
    r_id bigint,
    ur_right character(1),
    CONSTRAINT user_sec_profile_ur_right_check CHECK ((ur_right = ANY (ARRAY['R'::bpchar, 'W'::bpchar])))
);
CREATE TABLE profile_user (
    user_name text NOT NULL,
    pu_id integer NOT NULL,
    p_id integer
);
CREATE TABLE quant_fin (
    qf_id bigint NOT NULL,
    qf_bank bigint,
    jr_id bigint,
    qf_other bigint,
    qf_amount numeric(20,4) DEFAULT 0
);
CREATE TABLE quant_purchase (
    qp_id integer DEFAULT nextval(('s_quantity'::text)::regclass) NOT NULL,
    qp_internal text,
    j_id integer NOT NULL,
    qp_fiche integer NOT NULL,
    qp_quantite numeric(20,4) NOT NULL,
    qp_price numeric(20,4),
    qp_vat numeric(20,4) DEFAULT 0.0,
    qp_vat_code integer,
    qp_nd_amount numeric(20,4) DEFAULT 0.0,
    qp_nd_tva numeric(20,4) DEFAULT 0.0,
    qp_nd_tva_recup numeric(20,4) DEFAULT 0.0,
    qp_supplier integer NOT NULL,
    qp_valid character(1) DEFAULT 'Y'::bpchar NOT NULL,
    qp_dep_priv numeric(20,4) DEFAULT 0.0,
    qp_vat_sided numeric(20,4) DEFAULT 0.0,
    qp_unit numeric(20,4) DEFAULT 0
);
CREATE TABLE quant_sold (
    qs_id integer DEFAULT nextval(('s_quantity'::text)::regclass) NOT NULL,
    qs_internal text,
    qs_fiche integer NOT NULL,
    qs_quantite numeric(20,4) NOT NULL,
    qs_price numeric(20,4),
    qs_vat numeric(20,4),
    qs_vat_code integer,
    qs_client integer NOT NULL,
    qs_valid character(1) DEFAULT 'Y'::bpchar NOT NULL,
    j_id integer NOT NULL,
    qs_vat_sided numeric(20,4) DEFAULT 0.0,
    qs_unit numeric(20,4) DEFAULT 0
);
CREATE TABLE stock_change (
    c_id bigint NOT NULL,
    c_comment text,
    c_date date,
    tech_user text,
    r_id bigint,
    tech_date time without time zone DEFAULT now() NOT NULL
);
CREATE TABLE stock_goods (
    sg_id integer DEFAULT nextval(('s_stock_goods'::text)::regclass) NOT NULL,
    j_id integer,
    f_id integer,
    sg_code text,
    sg_quantity numeric(8,4) DEFAULT 0,
    sg_type character(1) DEFAULT 'c'::bpchar NOT NULL,
    sg_date date,
    sg_tech_date date DEFAULT now(),
    sg_tech_user text,
    sg_comment character varying(80),
    sg_exercice character varying(4),
    r_id bigint,
    c_id bigint,
    CONSTRAINT stock_goods_sg_type CHECK (((sg_type = 'c'::bpchar) OR (sg_type = 'd'::bpchar)))
);
CREATE TABLE stock_repository (
    r_id bigint NOT NULL,
    r_name text,
    r_adress text,
    r_country text,
    r_city text,
    r_phone text
);
CREATE TABLE tags (
    t_id integer NOT NULL,
    t_tag text NOT NULL,
    t_description text,
    t_actif character(1) DEFAULT 'Y'::bpchar,
    CONSTRAINT tags_check CHECK ((t_actif = ANY (ARRAY['N'::bpchar, 'Y'::bpchar])))
);
CREATE TABLE tmp_pcmn (
    pcm_val account_type NOT NULL,
    pcm_lib text,
    pcm_val_parent account_type DEFAULT 0,
    pcm_type text,
    id bigint DEFAULT nextval('tmp_pcmn_id_seq'::regclass) NOT NULL,
    pcm_direct_use character varying(1) DEFAULT 'Y'::character varying NOT NULL,
    CONSTRAINT pcm_direct_use_ck CHECK (((pcm_direct_use)::text = ANY ((ARRAY['Y'::character varying, 'N'::character varying])::text[])))
);
CREATE TABLE tmp_stockgood (
    s_id bigint NOT NULL,
    s_date timestamp without time zone DEFAULT now()
);
CREATE TABLE tmp_stockgood_detail (
    d_id bigint NOT NULL,
    s_id bigint,
    sg_code text,
    s_qin numeric(20,4),
    s_qout numeric(20,4),
    r_id bigint,
    f_id bigint
);
CREATE TABLE todo_list (
    tl_id integer DEFAULT nextval('todo_list_tl_id_seq'::regclass) NOT NULL,
    tl_date date NOT NULL,
    tl_title text NOT NULL,
    tl_desc text,
    use_login text NOT NULL,
    is_public character(1) DEFAULT 'N'::bpchar NOT NULL,
    CONSTRAINT ck_is_public CHECK ((is_public = ANY (ARRAY['Y'::bpchar, 'N'::bpchar])))
);
CREATE TABLE todo_list_shared (
    id integer NOT NULL,
    todo_list_id integer NOT NULL,
    use_login text NOT NULL
);
CREATE TABLE tool_uos (
    uos_value bigint DEFAULT nextval('uos_pk_seq'::regclass) NOT NULL
);
CREATE TABLE tva_rate (
    tva_id integer DEFAULT nextval('s_tva'::regclass) NOT NULL,
    tva_label text NOT NULL,
    tva_rate numeric(8,4) DEFAULT 0.0 NOT NULL,
    tva_comment text,
    tva_poste text,
    tva_both_side integer DEFAULT 0
);
CREATE TABLE user_active_security (
    id integer NOT NULL,
    us_login text NOT NULL,
    us_ledger character varying(1) NOT NULL,
    us_action character varying(1) NOT NULL,
    CONSTRAINT user_active_security_action_check CHECK (((us_action)::text = ANY ((ARRAY['Y'::character varying, 'N'::character varying])::text[]))),
    CONSTRAINT user_active_security_ledger_check CHECK (((us_ledger)::text = ANY ((ARRAY['Y'::character varying, 'N'::character varying])::text[])))
);
CREATE TABLE user_filter (
    id bigint NOT NULL,
    login text,
    nb_jrn integer,
    date_start character varying(10),
    date_end character varying(10),
    description text,
    amount_min numeric(20,4),
    amount_max numeric(20,4),
    qcode text,
    accounting text,
    r_jrn text,
    date_paid_start character varying(10),
    date_paid_end character varying(10),
    ledger_type character varying(5),
    all_ledger integer,
    filter_name text NOT NULL,
    unpaid character varying
);
CREATE TABLE user_local_pref (
    user_id text NOT NULL,
    parameter_type text NOT NULL,
    parameter_value text
);
CREATE TABLE user_sec_act (
    ua_id integer DEFAULT nextval(('s_user_act'::text)::regclass) NOT NULL,
    ua_login text,
    ua_act_id integer
);
CREATE TABLE user_sec_action_profile (
    ua_id bigint NOT NULL,
    p_id bigint,
    p_granted bigint,
    ua_right character(1),
    CONSTRAINT user_sec_action_profile_ua_right_check CHECK ((ua_right = ANY (ARRAY['R'::bpchar, 'W'::bpchar])))
);
CREATE TABLE user_sec_jrn (
    uj_id integer DEFAULT nextval(('s_user_jrn'::text)::regclass) NOT NULL,
    uj_login text,
    uj_jrn_id integer,
    uj_priv text
);
CREATE TABLE version (
    val integer NOT NULL,
    v_description text,
    v_date timestamp without time zone DEFAULT now()
);
