begin;

ALTER TABLE ac_dossier drop COLUMN dos_jnt_user ;
delete from jnt_use_dos where jnt_id in (select priv_jnt from priv_user where priv_priv='X');
delete from jnt_use_dos where use_id in (select use_id from ac_users where use_admin=1 or use_active=0);              
ALTER TABLE ac_users ADD COLUMN use_email text;
COMMENT ON COLUMN ac_users.use_email IS 'Email of the user';

CREATE OR REPLACE FUNCTION public.upgrade_repo(p_version integer)
 RETURNS void
AS $function$
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
$function$
 language plpgsql;

drop table priv_user;

CREATE TABLE recover_pass
(
  use_id bigint NOT NULL,
  request text NOT NULL,
  password text NOT NULL,
  created_on timestamp with time zone,
  created_host text,
  recover_on timestamp with time zone,
  recover_by text,
  CONSTRAINT recover_pass_pkey PRIMARY KEY (request ),
  CONSTRAINT ac_users_recover_pass_fk FOREIGN KEY (use_id)
      REFERENCES ac_users (use_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX fki_ac_users_recover_pass_fk  ON recover_pass  USING btree  (use_id );

select upgrade_repo(16);
commit;
