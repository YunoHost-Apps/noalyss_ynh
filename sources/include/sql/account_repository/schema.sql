
SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;







SET search_path = public, pg_catalog;


CREATE FUNCTION limit_user() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin                                                  
NEW.ac_user := substring(NEW.ac_user from 1 for 80);   
return NEW;                                            
end; $$;



CREATE FUNCTION upgrade_repo(p_version integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare 
        is_mono integer;
begin
        select count (*) into is_mono from information_schema.tables where table_name='repo_version';
        if is_mono = 1 then
                update repo_version set val=p_version;
        else
                update version set val=p_version;
        end if;
end;
$$;


SET default_tablespace = '';

SET default_with_oids = false;


CREATE TABLE ac_dossier (
    dos_id integer DEFAULT nextval(('dossier_id'::text)::regclass) NOT NULL,
    dos_name text NOT NULL,
    dos_description text,
    dos_email integer DEFAULT (-1)
);



COMMENT ON COLUMN ac_dossier.dos_email IS 'Max emails per day : 0 none , -1 unlimited or  max value';



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



COMMENT ON COLUMN ac_users.use_email IS 'Email of the user';



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



CREATE SEQUENCE audit_connect_ac_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



ALTER SEQUENCE audit_connect_ac_id_seq OWNED BY audit_connect.ac_id;



CREATE SEQUENCE dossier_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



CREATE TABLE dossier_sent_email (
    id integer NOT NULL,
    de_date character varying(8) NOT NULL,
    de_sent_email integer NOT NULL,
    dos_id integer NOT NULL
);



COMMENT ON TABLE dossier_sent_email IS 'Count the sent email by folder';



COMMENT ON COLUMN dossier_sent_email.id IS 'primary key';



COMMENT ON COLUMN dossier_sent_email.de_date IS 'Date YYYYMMDD';



COMMENT ON COLUMN dossier_sent_email.de_sent_email IS 'Number of sent emails';



COMMENT ON COLUMN dossier_sent_email.dos_id IS 'Link to ac_dossier';



CREATE SEQUENCE dossier_sent_email_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



ALTER SEQUENCE dossier_sent_email_id_seq OWNED BY dossier_sent_email.id;



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



CREATE SEQUENCE s_modid
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



CREATE SEQUENCE seq_jnt_use_dos
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



CREATE SEQUENCE seq_priv_user
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



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



COMMENT ON TABLE user_global_pref IS 'The user''s global parameter ';



COMMENT ON COLUMN user_global_pref.user_id IS 'user''s login ';



COMMENT ON COLUMN user_global_pref.parameter_type IS 'the type of parameter ';



COMMENT ON COLUMN user_global_pref.parameter_value IS 'the value of parameter ';



CREATE SEQUENCE users_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



CREATE TABLE version (
    val integer NOT NULL
);



















































CREATE INDEX audit_connect_ac_user ON audit_connect USING btree (ac_user);



CREATE INDEX fk_jnt_dos_id ON jnt_use_dos USING btree (dos_id);



CREATE INDEX fk_jnt_use_dos ON jnt_use_dos USING btree (use_id);



CREATE INDEX fki_ac_users_recover_pass_fk ON recover_pass USING btree (use_id);
























