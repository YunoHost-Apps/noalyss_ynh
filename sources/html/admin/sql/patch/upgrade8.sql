begin;

insert into action values (21,'Import et export des écritures d''ouverture');
create sequence s_quantity;


CREATE TABLE quant_sold (
    qs_id integer DEFAULT nextval('s_quantity'::text),
    qs_internal text NOT NULL,
    qs_fiche integer NOT NULL,
    qs_quantite integer NOT NULL,
    qs_price numeric(20,4),
    qs_vat numeric(20,4),
    qs_vat_code integer,
	qs_client integer not null
);

create index idx_qs_internal on quant_sold(qs_internal);

create table format_csv_banque 
(
	name text primary key,
	include_file text not null
);

create sequence s_invoice;

CREATE TABLE invoice (
    iv_id integer DEFAULT nextval('s_invoice'::text) NOT NULL,
    iv_name text NOT NULL,
    iv_file oid
);
alter TABLE invoice add  primary key(iv_id);
create unique index ix_iv_name on invoice (upper(iv_name));



-- drop trigger trim_space on format_csv_banque;
-- 
-- drop function trim_space_format_csv_banque();

create function trim_space_format_csv_banque() returns trigger as $trim$
declare
        modified format_csv_banque%ROWTYPE;
begin
        modified.name=trim(NEW.NAME);
        modified.include_file=trim(new.include_file);
		if ( length(modified.name) = 0 ) then
			modified.name=null;
		end if;
		if ( length(modified.include_file) = 0 ) then
			modified.include_file=null;
		end if;

        return modified;
end;
$trim$ language plpgsql;

create trigger trim_space before insert or update on format_csv_banque FOR EACH ROW execute procedure trim_space_format_csv_banque();

create unique index idx_case on format_csv_banque (upper(name));
INSERT INTO format_csv_banque VALUES ('Fortis', 'fortis_be.inc.php');
INSERT INTO format_csv_banque VALUES ('EUB', 'eub_be.inc.php');
INSERT INTO format_csv_banque VALUES ('ING', 'ing_be.inc.php');
INSERT INTO format_csv_banque VALUES ('CBC', 'cbc_be.inc.php');

CREATE TABLE import_tmp (
    code text not null,
    date_exec date not null ,
    date_valeur date not null,
    montant numeric(20,4) not null default 0,
    devise text,
    compte_ordre text,
    detail text,
    num_compte text,
    poste_comptable text,
    ok boolean DEFAULT false,
    bq_account integer not null,
	jrn integer not null
);
create function trim_cvs_quote() returns trigger as $trim$
declare
        modified import_tmp%ROWTYPE;
begin
		modified.code=new.code;
		modified.montant=new.montant;
		modified.date_exec=new.date_exec;
		modified.date_valeur=new.date_valeur;
		modified.devise=replace(new.devise,'"','');
		modified.poste_comptable=replace(new.poste_comptable,'"','');
        modified.compte_ordre=replace(NEW.COMPTE_ORDRE,'"','');
        modified.detail=replace(NEW.DETAIL,'"','');
        modified.num_compte=replace(NEW.NUM_COMPTE,'"','');
		modified.bq_account=NEW.bq_account;
		modified.jrn=NEW.jrn;
		modified.ok=new.ok;
        return modified;
end;
$trim$ language plpgsql;

create trigger trim_quote before insert or update on import_tmp FOR EACH ROW execute procedure trim_cvs_quote();
alter sequence s_attr_def restart 20;
insert into attr_def(ad_text) values ('Partie fiscalement non déductible');
insert into attr_def(ad_text) values ('TVA non déductible');
insert into attr_def(ad_text) values ('TVA non déductible récupérable par l''impôt');
insert into tmp_pcmn( pcm_val,pcm_lib,pcm_val_parent,pcm_country) select distinct 6190,'TVA récupérable par l''impôt',61,'BE' from tmp_pcmn where pcm_country='BE';
insert into tmp_pcmn( pcm_val,pcm_lib,pcm_val_parent,pcm_country) select distinct 6740,'Dépense non admise',67,'BE' from tmp_pcmn where pcm_country='BE' and not exists (select pcm_val from tmp_pcmn where pcm_val=6740);
-- Change for Stan alter table tmp_pcmn alter pcm_val type text;
update version set val=9;


commit;
