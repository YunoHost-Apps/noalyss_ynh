#!/bin/bash

export PGUSER=dany
export PGPASSWORD=dany
export PGHOST=localhost
export PGDATABASE=rel671mod1
export PGPORT=5432
(
echo "<?php "
psql -A -F"  " -t -c "select '\$menu[]=_('''||replace(me_menu,'''',E'\\\\''')||''');' , '\$desc[]=_('''||replace(me_description,'''',E'\\\\''')||''');' from menu_ref ;"
echo "?>"  ) > ../include/database.item.php
(
echo "<?php "
psql -A -F"  " -t -c "select '\$attr_def[]=_('''||replace(ad_text,'''',E'\\\\''')||''');' from attr_def ;"
echo "?>"  ) >> ../include/database.item.php
(
echo "<?php "
psql -A -F"  " -t -c "select '\$document_type[]=_('''||replace(dt_value,'''',E'\\\\''')||''');' from document_type ;"
echo "?>"  ) >> ../include/database.item.php
(
echo "<?php "
psql -A -F"  " -t -c "select '\$action[]=_('''||replace(ac_description,'''',E'\\\\''')||''');' from action ;"
echo "?>"  ) >> ../include/database.item.php
(
echo "<?php "
psql -A -F"  " -t -c "select '\$mdp[]=_('''||replace(mp_lib,'''',E'\\\\''')||''');' from mod_payment ;"
echo "?>"  ) >> ../include/database.item.php
(
echo "<?php "
psql -A -F"  " -t -c "select '\$jrn_def_name[]=_('''||replace(jrn_def_name,'''',E'\\\\''')||''');' from jrn_def ;"
echo "?>"  ) >> ../include/database.item.php
(
echo "<?php "
psql -A -F"  " -t -c "select '\$jrn_def_description[]=_('''||replace(jrn_def_description,'''',E'\\\\''')||''');' from jrn_def ;"
echo "?>"  ) >> ../include/database.item.php

echo "File ../include/database.item.php is created"
