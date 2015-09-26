begin;
CREATE OR REPLACE FUNCTION comptaproc.format_account(p_account account_type)
  RETURNS account_type AS
$BODY$

declare

sResult account_type;

begin
sResult := lower(p_account);

sResult := translate(sResult,E'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');
sResult := translate(sResult,E' $€µ£%.+-/\\!(){}(),;_&|"#''^<>*','');

return upper(sResult);

end;
$BODY$
  LANGUAGE plpgsql ;

COMMENT ON FUNCTION comptaproc.format_account(account_type) IS 'format the accounting :
- upper case
- remove space and special char.
';

update tmp_pcmn  set pcm_val_parent  = '62' where pcm_val='6202';

update fiche_detail set ad_value = (to_number(ad_value,'9.99')*100)::text where ad_id in  (21,22,20,31) and ad_value is not null and ad_value <> '';

update menu_ref set me_code='ACHISTO' WHERE me_code='ACHIMP';
update menu_ref set me_code='VEHISTO' WHERE me_code='VENIMP';
update menu_ref set me_code='FIHISTO' WHERE me_code='FIMP';
update menu_ref set me_code='ODHISTO' WHERE me_code='ODSIMP';
update version set val=104;

commit;