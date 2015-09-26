begin;

create table parm_code (
	p_code text primary key,
	p_value text,
	p_comment text
);

INSERT INTO parm_code VALUES ('DNA', '6740', 'Dépense non déductible');
INSERT INTO parm_code VALUES ('CUSTOMER', '400', 'Poste comptable de base pour les clients');
INSERT INTO parm_code VALUES ('COMPTE_TVA', '451', 'TVA à payer');
INSERT INTO parm_code VALUES ('BANQUE', '550', 'Poste comptable de base pour les banques');
INSERT INTO parm_code VALUES ('VIREMENT_INTERNE', '58', 'Poste Comptable pour les virements internes');
INSERT INTO parm_code VALUES ('COMPTE_COURANT', '56', 'Poste comptable pour le compte courant');
INSERT INTO parm_code VALUES ('CAISSE', '57', 'Poste comptable pour la caisse');
INSERT INTO parm_code VALUES ('TVA_DNA', '6740', 'Tva non déductible s');
INSERT INTO parm_code VALUES ('TVA_DED_IMPOT', '619000', 'Tva déductible par l''impôt');
INSERT INTO parm_code VALUES ('VENTE ', '70', 'Poste comptable de base pour les ventes');

-- Function: tva_delete(p_tva_id int4)

CREATE OR REPLACE FUNCTION tva_delete(int4)
  RETURNS void AS
$BODY$ 
declare
	p_tva_id alias for $1;
	nCount integer;
begin
	nCount=0;
	select count(*) into nCount from quant_sold where qs_vat_code=p_tva_id;
	if nCount = 0 then
		delete from tva_rate where tva_id=p_tva_id;
	end if;
	return;
end;
$BODY$ 
LANGUAGE plpgsql VOLATILE;

-- Function: tva_insert(int4, text, numeric, text, text)

CREATE OR REPLACE FUNCTION tva_insert(int4, text, "numeric", text, text)
  RETURNS int4 AS
$BODY$
declare
p_tva_id alias for $1;
p_tva_label alias for $2;
p_tva_rate alias for $3;
p_tva_comment alias for $4;
p_tva_poste alias for $5;
debit text;
credit text;
nCount integer;
begin
-- verify that label is not null
if length(trim(p_tva_label)) = 0 then
	return 3;
end if;
select count(*) into nCount from tva_rate 
	where tva_id=p_tva_id;
if nCount != 0 then
	return 5;
end if;
-- check is poste exists
if length(trim(p_tva_poste)) != 0 then
-- check if it is a comma list
	if position (',' in p_tva_poste) = 0 then return 4; end if;
-- separate "credit" and "debit"
	debit  = split_part(p_tva_poste,',',1);
	credit  = split_part(p_tva_poste,',',2);
-- check if those account exist
	select count(*) into nCount from tmp_pcmn where pcm_val=debit;
	if nCount = 0 then return 4; end if;
	select count(*) into nCount from tmp_pcmn where pcm_val=credit;
	if nCount = 0 then return 4; end if;
 
end if;
insert into tva_rate(tva_id,tva_label,tva_rate,tva_comment,tva_poste)
	values (p_tva_id,p_tva_label,p_tva_rate,p_tva_comment,p_tva_poste);
return 0;
end;
$BODY$
LANGUAGE plpgsql VOLATILE;

-- Function: tva_insert(p_tva_id text, p_tva_label text, p_tva_rate text, p_tva_comment text, p_tva_poste text)

CREATE OR REPLACE FUNCTION tva_modify(integer, text, numeric, text, text)
  RETURNS int4 AS
$BODY$declare
p_tva_id alias for $1;
p_tva_label alias for $2;
p_tva_rate alias for $3;
p_tva_comment alias for $4;
p_tva_poste alias for $5;
debit text;
credit text;
nCount integer;
begin
-- verify that label is not null
if length(trim(p_tva_label)) = 0 then
	return 3;
end if;

-- check is poste exists
if length(trim(p_tva_poste)) != 0 then
-- check if it is a comma list
	if position (',' in p_tva_poste) = 0 then return 4; end if;
-- separate "credit" and "debit"
	debit  = split_part(p_tva_poste,',',1);
	credit  = split_part(p_tva_poste,',',2);
-- check if those account exist
	select count(*) into nCount from tmp_pcmn where pcm_val=debit;
	if nCount = 0 then return 4; end if;
	select count(*) into nCount from tmp_pcmn where pcm_val=credit;
	if nCount = 0 then return 4; end if;
 
end if;
update tva_rate set tva_label=p_tva_label,tva_rate=p_tva_rate,tva_comment=p_tva_comment,tva_poste=p_tva_poste
	where tva_id=p_tva_id;
return 0;
end;
$BODY$
LANGUAGE plpgsql VOLATILE;


update version set val=12;
commit;
