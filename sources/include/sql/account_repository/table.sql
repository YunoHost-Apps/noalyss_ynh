CREATE TABLE ac_dossier (
    dos_id integer DEFAULT nextval(('dossier_id'::text)::regclass) NOT NULL,
    dos_name text NOT NULL,
    dos_description text,
    dos_email integer DEFAULT (-1)
);
CREATE TABLE ac_users (
    use_id integer DEFAULT nextval(('users_id'::text)::regclass) NOT NULL,
    use_first_name text,
    use_name text,
    use_login text NOT NULL,
    use_active integer DEFAULT 0,
    use_pass text,
    use_admin integer DEFAULT 0,
    use_email text,
    CONSTRAINT ac_users_use_active_check CHECK (((use_active = 0) OR (use_active = 1)))
);
CREATE TABLE audit_connect (
    ac_id integer NOT NULL,
    ac_user text,
    ac_date timestamp without time zone DEFAULT now(),
    ac_ip text,
    ac_state text,
    ac_module text,
    ac_url text,
    CONSTRAINT valid_state CHECK ((((ac_state = 'FAIL'::text) OR (ac_state = 'SUCCESS'::text)) OR (ac_state = 'AUDIT'::text)))
);
CREATE TABLE dossier_sent_email (
    id integer NOT NULL,
    de_date character varying(8) NOT NULL,
    de_sent_email integer NOT NULL,
    dos_id integer NOT NULL
);
CREATE TABLE jnt_use_dos (
    jnt_id integer DEFAULT nextval(('seq_jnt_use_dos'::text)::regclass) NOT NULL,
    use_id integer NOT NULL,
    dos_id integer NOT NULL
);
CREATE TABLE modeledef (
    mod_id integer DEFAULT nextval(('s_modid'::text)::regclass) NOT NULL,
    mod_name text NOT NULL,
    mod_desc text
);
CREATE TABLE progress (
    p_id character varying(16) NOT NULL,
    p_value numeric(5,2) NOT NULL,
    p_created timestamp without time zone DEFAULT now()
);
CREATE TABLE recover_pass (
    use_id bigint NOT NULL,
    request text NOT NULL,
    password text NOT NULL,
    created_on timestamp with time zone,
    created_host text,
    recover_on timestamp with time zone,
    recover_by text
);
CREATE TABLE theme (
    the_name text NOT NULL,
    the_filestyle text,
    the_filebutton text
);
CREATE TABLE user_global_pref (
    user_id text NOT NULL,
    parameter_type text NOT NULL,
    parameter_value text
);
CREATE TABLE version (
    val integer NOT NULL
);
