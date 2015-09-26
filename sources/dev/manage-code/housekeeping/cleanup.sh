#!/bin/bash
# clean all phpcompta related DB.
DOMAIN="rel500_"
export PGPASSWORD="dany"
export PGUSER="dany"
export PGHOST=localhost
echo "Etes vous sur de vouloir effacer les db du domaine $DOMAIN Y/N ?"
read A
if [ "$A" == 'Y' ];then
    dropdb  ${DOMAIN}account_repository
    dropdb  ${DOMAIN}dossier1
    dropdb   ${DOMAIN}dossier3
    dropdb   ${DOMAIN}dossier4
    dropdb   ${DOMAIN}dossier5
    dropdb   ${DOMAIN}dossier13
    dropdb   ${DOMAIN}dossier17

    dropdb   ${DOMAIN}mod1
    dropdb   ${DOMAIN}mod2
    dropdb   ${DOMAIN}mod3
    dropdb   ${DOMAIN}mod7
else 
    echo "Effacement annule"
fi
