--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

SET search_path = public, pg_catalog;

--
-- Name: limit_user(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION limit_user() RETURNS trigger
    AS $$

begin
NEW.ac_user := substring(NEW.ac_user from 1 for 80);
return NEW;
end; 
$$
LANGUAGE plpgsql;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: ac_dossier; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ac_dossier (
    dos_id integer DEFAULT nextval(('dossier_id'::text)::regclass) NOT NULL,
    dos_name text NOT NULL,
    dos_description text,
    dos_jnt_user integer DEFAULT 0
);


--
-- Name: ac_users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ac_users (
    use_id integer DEFAULT nextval(('users_id'::text)::regclass) NOT NULL,
    use_first_name text,
    use_name text,
    use_login text NOT NULL,
    use_active integer DEFAULT 0,
    use_pass text,
    use_admin integer DEFAULT 0,
    CONSTRAINT ac_users_use_active_check CHECK (((use_active = 0) OR (use_active = 1)))
);


--
-- Name: audit_connect; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

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


--
-- Name: audit_connect_ac_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE audit_connect_ac_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: audit_connect_ac_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE audit_connect_ac_id_seq OWNED BY audit_connect.ac_id;


--
-- Name: dossier_id; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE dossier_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jnt_use_dos; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jnt_use_dos (
    jnt_id integer DEFAULT nextval(('seq_jnt_use_dos'::text)::regclass) NOT NULL,
    use_id integer NOT NULL,
    dos_id integer NOT NULL
);


--
-- Name: modeledef; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE modeledef (
    mod_id integer DEFAULT nextval(('s_modid'::text)::regclass) NOT NULL,
    mod_name text NOT NULL,
    mod_desc text
);


--
-- Name: priv_user; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE priv_user (
    priv_id integer DEFAULT nextval(('seq_priv_user'::text)::regclass) NOT NULL,
    priv_jnt integer NOT NULL,
    priv_priv text
);


--
-- Name: repo_version; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE repo_version (
    val integer NOT NULL
);


--
-- Name: s_modid; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_modid
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: seq_jnt_use_dos; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_jnt_use_dos
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: seq_priv_user; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_priv_user
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: theme; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE theme (
    the_name text NOT NULL,
    the_filestyle text,
    the_filebutton text
);


--
-- Name: user_global_pref; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_global_pref (
    user_id text NOT NULL,
    parameter_type text NOT NULL,
    parameter_value text
);


--
-- Name: TABLE user_global_pref; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE user_global_pref IS 'The user''s global parameter ';


--
-- Name: COLUMN user_global_pref.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN user_global_pref.user_id IS 'user''s login ';


--
-- Name: COLUMN user_global_pref.parameter_type; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN user_global_pref.parameter_type IS 'the type of parameter ';


--
-- Name: COLUMN user_global_pref.parameter_value; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN user_global_pref.parameter_value IS 'the value of parameter ';


--
-- Name: users_id; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ac_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY audit_connect ALTER COLUMN ac_id SET DEFAULT nextval('audit_connect_ac_id_seq'::regclass);


--
-- Data for Name: ac_dossier; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO ac_dossier VALUES (25, 'Dossier', 'Dossier par défaut', 0);


--
-- Data for Name: ac_users; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO ac_users VALUES (1, NULL, NULL, 'phpcompta', 1, 'b1cc88e1907cde80cb2595fa793b3da9', 1);


--
-- Data for Name: audit_connect; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: audit_connect_ac_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('audit_connect_ac_id_seq', 287, true);


--
-- Name: dossier_id; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('dossier_id', 29, true);


--
-- Data for Name: jnt_use_dos; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO jnt_use_dos VALUES (29, 1, 25);


--
-- Data for Name: modeledef; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO modeledef VALUES (1, '(BE) Basique', 'Comptabilité Belge, à adapter');
INSERT INTO modeledef VALUES (2, '(FR) Basique', 'Comptabilité Française, à adapter');


--
-- Data for Name: priv_user; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: repo_version; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO repo_version VALUES (15);


--
-- Name: s_modid; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_modid', 8, true);


--
-- Name: seq_jnt_use_dos; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_jnt_use_dos', 33, true);


--
-- Name: seq_priv_user; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_priv_user', 16, true);


--
-- Data for Name: theme; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO theme VALUES ('Light', 'style-light.css', NULL);
INSERT INTO theme VALUES ('Mandarine', 'style-mandarine.css', NULL);
INSERT INTO theme VALUES ('Mobile', 'style-mobile.css', NULL);
INSERT INTO theme VALUES ('Classique', 'style-classic.css', NULL);


--
-- Data for Name: user_global_pref; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO user_global_pref VALUES ('phpcompta', 'TOPMENU', 'TEXT');
INSERT INTO user_global_pref VALUES ('phpcompta', 'PAGESIZE', '50');
INSERT INTO user_global_pref VALUES ('phpcompta', 'LANG', 'fr_FR.utf8');
INSERT INTO user_global_pref VALUES ('phpcompta', 'THEME', 'Classique');


--
-- Name: users_id; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('users_id', 5, true);


--
-- Name: ac_dossier_dos_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ac_dossier
    ADD CONSTRAINT ac_dossier_dos_name_key UNIQUE (dos_name);


--
-- Name: ac_dossier_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ac_dossier
    ADD CONSTRAINT ac_dossier_pkey PRIMARY KEY (dos_id);


--
-- Name: ac_users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ac_users
    ADD CONSTRAINT ac_users_pkey PRIMARY KEY (use_id);


--
-- Name: ac_users_use_login_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ac_users
    ADD CONSTRAINT ac_users_use_login_key UNIQUE (use_login);


--
-- Name: audit_connect_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY audit_connect
    ADD CONSTRAINT audit_connect_pkey PRIMARY KEY (ac_id);


--
-- Name: jnt_use_dos_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jnt_use_dos
    ADD CONSTRAINT jnt_use_dos_pkey PRIMARY KEY (jnt_id);


--
-- Name: modeledef_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY modeledef
    ADD CONSTRAINT modeledef_pkey PRIMARY KEY (mod_id);


--
-- Name: pk_user_global_pref; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_global_pref
    ADD CONSTRAINT pk_user_global_pref PRIMARY KEY (user_id, parameter_type);


--
-- Name: priv_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY priv_user
    ADD CONSTRAINT priv_user_pkey PRIMARY KEY (priv_id);


--
-- Name: repo_version_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY repo_version
    ADD CONSTRAINT repo_version_pkey PRIMARY KEY (val);


--
-- Name: audit_connect_ac_user; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX audit_connect_ac_user ON audit_connect USING btree (ac_user);


--
-- Name: fk_jnt_dos_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fk_jnt_dos_id ON jnt_use_dos USING btree (dos_id);


--
-- Name: fk_jnt_use_dos; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fk_jnt_use_dos ON jnt_use_dos USING btree (use_id);


--
-- Name: limit_user_trg; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER limit_user_trg BEFORE INSERT OR UPDATE ON audit_connect FOR EACH ROW EXECUTE PROCEDURE limit_user();


--
-- Name: fk_user_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_global_pref
    ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES ac_users(use_login) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jnt_use_dos_dos_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jnt_use_dos
    ADD CONSTRAINT jnt_use_dos_dos_id_fkey FOREIGN KEY (dos_id) REFERENCES ac_dossier(dos_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jnt_use_dos_use_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jnt_use_dos
    ADD CONSTRAINT jnt_use_dos_use_id_fkey FOREIGN KEY (use_id) REFERENCES ac_users(use_id);


--
-- Name: priv_user_priv_jnt_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY priv_user
    ADD CONSTRAINT priv_user_priv_jnt_fkey FOREIGN KEY (priv_jnt) REFERENCES jnt_use_dos(jnt_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

