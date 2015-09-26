begin;

create or replace function card_class_base(p_f_id fiche.f_id%type) 
returns fiche_def.fd_class_base%type
as 
$$
declare
	n_poste fiche_def.fd_class_base%type;
begin
-- card_class_base (integer)
-- param: $1 fiche.f_id
-- purpose : retrieve the class of a card
-- 

	select fd_class_base into n_poste from fiche_def join fiche using
(fd_id)
	where f_id=p_f_id;
	if not FOUND then 
		raise exception 'Invalid fiche card_class_base(%)',p_f_id;
	end if;
return n_poste;
end; 
$$ language plpgsql;
update version set val=17;
commit;
