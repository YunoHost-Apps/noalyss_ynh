begin;


alter table profile_menu add pm_id_dep bigint ;
comment on column profile_menu.pm_id_dep is 'parent of this menu item';


CREATE OR REPLACE VIEW v_menu_dependency AS 
 WITH t_menu AS (
         SELECT pm.pm_id, mr.me_menu, pm.me_code, pm.me_code_dep, pm.p_type_display, mr.me_file, mr.me_javascript, mr.me_description, mr.me_description_etendue, p.p_id
           FROM profile_menu pm
      JOIN profile p ON p.p_id = pm.p_id
   JOIN menu_ref mr USING (me_code)
        )
 SELECT DISTINCT (COALESCE(v3.me_code || '/'::text, ''::text) || COALESCE(v2.me_code, ''::text)) || 
        CASE
            WHEN v2.me_code IS NULL THEN COALESCE(v1.me_code, ''::text)
            WHEN v2.me_code IS NOT NULL THEN COALESCE('/'::text || v1.me_code, ''::text)
            ELSE NULL::text
        END AS code, v1.pm_id, v1.me_code, v1.me_description, v1.me_description_etendue, v1.me_file, '> '::text || v1.me_menu AS v1menu, 
        CASE
            WHEN v2.pm_id IS NOT NULL THEN v2.pm_id
            WHEN v3.pm_id IS NOT NULL THEN v3.pm_id
            ELSE NULL::integer
        END AS higher_dep, 
        CASE
            WHEN COALESCE(v3.me_menu, ''::text) <> ''::text THEN ' > '::text || v2.me_menu
            ELSE v2.me_menu
        END AS v2menu, v3.me_menu AS v3menu, v3.p_type_display, COALESCE(v1.me_javascript, COALESCE(v2.me_javascript, v3.me_javascript)) AS javascript, v1.p_id, v2.p_id AS v2pid, v3.p_id AS v3pid
   FROM t_menu v1
   LEFT JOIN t_menu v2 ON v1.me_code_dep = v2.me_code
   LEFT JOIN t_menu v3 ON v2.me_code_dep = v3.me_code
  WHERE COALESCE(v2.p_id, v1.p_id) = v1.p_id AND COALESCE(v3.p_id, v1.p_id) = v1.p_id AND v1.p_type_display <> 'P'::text
  ORDER BY v1.pm_id;

CREATE OR REPLACE FUNCTION modify_menu_system(n_profile numeric)
  RETURNS void AS
$BODY$
declare 
r_duplicate profile_menu%ROWTYPE;
str_duplicate text;
n_lowest_id numeric; -- lowest pm_id : update the dependency in profile_menu
n_highest_id numeric; -- highest pm_id insert into profile_menu

begin

for str_duplicate in   
	select me_code 
	from profile_menu 
	where 
	p_id=n_profile and 
	p_type_display <> 'P' and
	pm_id_dep is null
	group by me_code 
	having count(*) > 1 
loop
	raise info 'str_duplicate %',str_duplicate;
	for r_duplicate in select * 
		from profile_menu 
		where 
		p_id=n_profile and
		me_code_dep=str_duplicate
	loop
		raise info 'r_duplicate %',r_duplicate;
		-- get the lowest 
		select a.pm_id into n_lowest_id from profile_menu a join profile_menu b on (a.me_code=b.me_code and a.p_id = b.p_id)
		where
		a.me_code=str_duplicate
		and a.p_id=n_profile
		and a.pm_id < b.pm_id;
		raise info 'lowest is %',n_lowest_id;
		-- get the highest
		select a.pm_id into n_highest_id from profile_menu a join profile_menu b on (a.me_code=b.me_code and a.p_id = b.p_id)
		where
		a.me_code=str_duplicate
		and a.p_id=n_profile
		and a.pm_id > b.pm_id;
		raise info 'highest is %',n_highest_id;

		-- update the first one
		update profile_menu set pm_id_dep = n_lowest_id where pm_id=r_duplicate.pm_id;
		-- insert a new one
		insert into profile_menu (me_code,
			me_code_dep,
			p_id,
			p_order,
			p_type_display,
			pm_default,
			pm_id_dep)
		values (r_duplicate.me_code,
			r_duplicate.me_code_dep,
			r_duplicate.p_id,
			r_duplicate.p_order,
			r_duplicate.p_type_display,
			r_duplicate.pm_default,
			n_highest_id);
		
	end loop;	

end loop;	
end;
$BODY$
language plpgsql;

select modify_menu_system(1);
select modify_menu_system(2);

update profile_menu set pm_id_dep=(select higher_dep from v_menu_dependency as a where
 a.pm_id= profile_menu.pm_id) where pm_id_dep is null and p_id=1;

update profile_menu set pm_id_dep=(select higher_dep from v_menu_dependency as a where
 a.pm_id= profile_menu.pm_id) where pm_id_dep is null and p_id=2;
CREATE OR REPLACE VIEW v_menu_profile AS 
 WITH t_menu AS (
         SELECT pm.pm_id,pm.pm_id_dep, pm.me_code, pm.me_code_dep, pm.p_type_display,pm.p_id
           FROM profile_menu pm
   JOIN profile p ON p.p_id = pm.p_id
   )
 SELECT DISTINCT 
	(COALESCE(v3.me_code || '/'::text, ''::text) || COALESCE(v2.me_code, ''::text)) || 
        CASE
            WHEN v2.me_code IS NULL THEN COALESCE(v1.me_code, ''::text)
            WHEN v2.me_code IS NOT NULL THEN COALESCE('/'::text || v1.me_code, ''::text)
            ELSE NULL::text
        END AS code, 
        v3.p_type_display,
        coalesce(v3.pm_id,0) as pm_id_v3,
	coalesce(v2.pm_id,0) as pm_id_v2,
        v1.pm_id as pm_id_v1
        ,v1.p_id
   FROM t_menu v1
   LEFT JOIN t_menu v2 ON v1.pm_id_dep = v2.pm_id
   LEFT JOIN t_menu v3 ON v2.pm_id_dep= v3.pm_id
  WHERE v1.p_type_display <> 'P'::text 
;
COMMENT ON VIEW v_menu_profile  IS 'Give the profile and the menu + dependencies';

CREATE OR REPLACE VIEW v_menu_description AS 
 WITH t_menu AS (
         SELECT pm.pm_id,pm.pm_id_dep,pm.p_id,mr.me_menu, pm.me_code, pm.me_code_dep, pm.p_type_display, pu.user_name, mr.me_file, mr.me_javascript, mr.me_description, mr.me_description_etendue
           FROM profile_menu pm
      JOIN profile_user pu ON pu.p_id = pm.p_id
   JOIN profile p ON p.p_id = pm.p_id
   JOIN menu_ref mr USING (me_code)
        )
 SELECT DISTINCT (COALESCE(v3.me_code || '/'::text, ''::text) || COALESCE(v2.me_code, ''::text)) || 
        CASE
            WHEN v2.me_code IS NULL THEN COALESCE(v1.me_code, ''::text)
            WHEN v2.me_code IS NOT NULL THEN COALESCE('/'::text || v1.me_code, ''::text)
            ELSE NULL::text
        END AS code, v1.me_code, v1.me_description, v1.me_description_etendue, v1.me_file, v1.user_name, '> '::text || v1.me_menu AS v1menu, 
        CASE
            WHEN COALESCE(v3.me_menu, ''::text) <> ''::text THEN ' > '::text || v2.me_menu
            ELSE v2.me_menu
        END AS v2menu, v3.me_menu AS v3menu, v3.p_type_display, COALESCE(v1.me_javascript, COALESCE(v2.me_javascript, v3.me_javascript)) AS javascript,
        v1.pm_id,v1.pm_id_dep,v1.p_id
   FROM t_menu v1
   LEFT JOIN t_menu v2 ON v1.me_code_dep = v2.me_code
   LEFT JOIN t_menu v3 ON v2.me_code_dep = v3.me_code
  WHERE v1.p_type_display <> 'P'::text AND (COALESCE(v1.me_file, ''::text) <> ''::text OR COALESCE(v1.me_javascript, ''::text) <> ''::text);

COMMENT ON VIEW v_menu_description  IS 'Description des menus';


update version set val=119;

commit;
