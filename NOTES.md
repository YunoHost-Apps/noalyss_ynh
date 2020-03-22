Noalyss pour YunoHost
---------------------

[noalyss](http://noalyss.eu) est une application de comptabilité que vous pourrez modifier suivant vos envies.

 * Report a bug: https://dev.yunohost.org/projects/apps/issues
 * Nextcloud website: https://nextcloud.com/
 * YunoHost website: https://yunohost.org/

# Questions

- comment fonctionnent les mails avec Noalyss ?


## PDF ?
voir : http://wiki.noalyss.eu/doku.php?id=installation:installation_sous_linux
- Pas de conversion en PDF (extension facturation et listing)
- Pas d'export en PDF des opérations de la comptabilité analytique avec les documents attachés


# Base de travail

[example_ynh](https://github.com/YunoHost/example_ynh) a été utilisé comme source(s) d'inspiration.

## Les helpers de YunoHost

Dans yunohost/data/helpers.d

- J'ai utilisé le `psql` pour en faire une copie en tant que `_psql.sh` pour pouvoir créer de user noalyss de postgress avec les droits de createdb.

# Les sources de Noalyss

**Version:** 7.0.15 (7015)

Il faut les sources de Noalyss et différentes versions sont disponibles.

http://download.noalyss.eu/version-6.9/
http://download.noalyss.eu/version-6.9/noalyss-6.9.1.9.tar.gz
http://download.noalyss.eu/version-6.9/noalyss-6.9.2.0.tar.gz

http://download.noalyss.eu/version-7.0/
http://download.noalyss.eu/version-7.0/noalyss-7015.tar.gz

Pour le moment elles sont extraites à la mains dans le dossier `sources`.

Mais ont pourrait imaginer que le script

- télécharge le .tar.gz
- l'extraire au bon endroit

# Quid des mises à jours ?

Pour Nextcloud par exemple il y a un dossier `upgrade.d` dans `scripts`.  Mais si je ne me trompe, pour Noalyss il « suffit » de remplacer le dossier des sources.

