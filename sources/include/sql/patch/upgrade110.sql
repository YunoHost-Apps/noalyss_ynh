begin;
create or replace view v_menu_description_favori as
 WITH t_menu AS (
         SELECT mr.me_menu, pm.me_code, pm.me_code_dep, pm.p_type_display, pu.user_name, mr.me_file, mr.me_javascript, mr.me_description, mr.me_description_etendue
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
        END AS v2menu, v3.me_menu AS v3menu, v3.p_type_display, COALESCE(v1.me_javascript, COALESCE(v2.me_javascript, v3.me_javascript)) AS javascript
   FROM t_menu v1
   LEFT JOIN t_menu v2 ON v1.me_code_dep = v2.me_code
   LEFT JOIN t_menu v3 ON v2.me_code_dep = v3.me_code
  WHERE v1.p_type_display <> 'P'::text;
update version set val=111;

commit;
