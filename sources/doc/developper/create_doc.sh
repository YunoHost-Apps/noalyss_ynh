doxygen
cd html; sed -i "s/utf-8/utf-8/g" *
cd ..
if [ ! -z "$PGUSER" ] ; then
	echo "DOMAINE = $DOMAIN"
	postgresql_autodoc -u $PGUSER --password=$PGPASSWORD -h localhost -d ${DOMAIN}mod1
	postgresql_autodoc  -u $PGUSER --password=$PGPASSWORD  -h localhost -d ${DOMAIN}account_repository
fi
cd ../../ && bash dev/compose_list.sh
