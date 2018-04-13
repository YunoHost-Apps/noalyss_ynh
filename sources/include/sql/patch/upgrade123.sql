begin;
CREATE OR REPLACE FUNCTION comptaproc.jrnx_ins()
 RETURNS trigger
 
AS $function$
declare
n_fid bigint;
nCount integer;
sQcode text;

begin
n_fid := null;
NEW.j_tech_per := comptaproc.find_periode(to_char(NEW.j_date,'DD.MM.YYYY'));
if NEW.j_tech_per = -1 then
        raise exception 'Période invalide';
end if;

NEW.j_qcode=trim(upper(NEW.j_qcode));

if coalesce(NEW.j_qcode,'') = '' then
        -- how many card has this accounting
        select count(*) into nCount from fiche_detail where ad_id=5 and ad_value=NEW.j_poste;
        -- only one card is found , then we change the j_qcode by the card
        if nCount = 1 then
                select f_id into n_fid from fiche_detail where ad_id = 5 and ad_value=NEW.j_poste;
                select ad_value into sQcode  from fiche_detail where f_id=n_fid and ad_id = 23;
                NEW.f_id := n_fid;
                NEW.j_qcode = trim(sQcode);
        end if;

end if;



if length (NEW.j_qcode) = 0 then
    NEW.j_qcode=NULL;
    else
   select f_id into n_fid from fiche_detail  where ad_id=23 and ad_value=NEW.j_qcode;
if NOT FOUND then
raise exception 'La fiche dont le quick code est % n''existe pas',NEW.j_qcode;
end if;
end if;
NEW.f_id:=n_fid;
return NEW;
end;
$function$
LANGUAGE plpgsql;


update version set val=124;

commit;