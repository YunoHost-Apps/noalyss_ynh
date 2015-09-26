begin;
-- Table: audit_connect

-- DROP TABLE audit_connect;

CREATE TABLE audit_connect
(
  ac_id serial NOT NULL,
  ac_user text,
  ac_date timestamp without time zone DEFAULT now(),
  ac_ip text,
  ac_state text,
  ac_module text,
  ac_url text,
  CONSTRAINT audit_connect_pkey PRIMARY KEY (ac_id),
  CONSTRAINT valid_state CHECK (ac_state = 'FAIL'::text OR ac_state = 'SUCCESS'::text)
);

CREATE OR REPLACE FUNCTION limit_user()
  RETURNS trigger AS
$BODY$

begin                                                  
NEW.ac_user := substring(NEW.ac_user from 1 for 80);   
return NEW;                                            
end; $BODY$
LANGUAGE plpgsql;

CREATE TRIGGER limit_user_trg
  BEFORE INSERT OR UPDATE
  ON audit_connect
  FOR EACH ROW
  EXECUTE PROCEDURE limit_user();

  
-- Index: audit_connect_ac_user

-- DROP INDEX audit_connect_ac_user;

CREATE INDEX audit_connect_ac_user
  ON audit_connect
  USING btree
  (ac_user);

update version set val=13;
commit;
