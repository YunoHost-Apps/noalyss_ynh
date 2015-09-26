begin;
update theme set the_name='Mandarine' ,the_filestyle='style-mandarine.css' where the_name='Colored';
update theme set the_name='Mobile' ,the_filestyle='style-mobile.css' where the_name='EPad';
update theme set the_name = 'Classique' where the_name='classic';
update user_global_pref set parameter_value='Classique' where parameter_type='THEME';
update theme set the_filestyle='style-classic.css' where the_filestyle='style.css';

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
select upgrade_repo(15);

commit;
