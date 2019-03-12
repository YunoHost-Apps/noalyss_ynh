# Noalyss pour YunoHost

TODO [![Integration level](https://dash.yunohost.org/integration/REPLACEBYYOURAPP.svg)](https://dash.yunohost.org/appci/app/REPLACEBYYOURAPP)  
TODO [![Install REPLACEBYYOURAPP with YunoHost](https://install-app.yunohost.org/install-with-yunohost.png)](https://install-app.yunohost.org/?app=REPLACEBYYOURAPP)

*[Read this readme in english.](./README.md)*

> *Ce package vous permet d'installer Noalyss rapidement et simplement sur un serveur Yunohost.  
Si vous n'avez pas YunoHost, regardez [ici](https://yunohost.org/#/install) pour savoir comment l'installer et en profiter.*

## Vue d'ensemble

[Noalyss](http://noalyss.eu) est une application de comptabilité belge et française que vous pourrez modifier suivant vos envies.  L'interface est disponible en anglais, français et néerlandais.

**Version incluse:** 7.0.15

## Captures d'écran

![](https://framalibre.org/sites/default/files/S%C3%A9lection_099_0.png)

## Démo

* [Démo officielle](http://demo.noalyss.eu/index.php)

## Configuration

À la fin de l'installation de l'application il faut se rendre sur https://domaine/chemin/install.php pour terminer la procédure.

## Documentation

* [documentation officielle](http://www.noalyss.eu/?page_id=1031).

## Caractéristiques spécifiques YunoHost

#### Support multi-utilisateurs

* Pas d'authentification LDAP, les utilisateur·ice·s sont gérés dans l'application.
* Plusieurs utilisateur·ice·s sont donc possibles et disponibles pour les différents dossiers comptables.  Une gestion des accès est prise en charge dans Noalyss.

#### Architectures supportées

* TODO x86-64b - [![Build Status](https://ci-apps.yunohost.org/ci/logs/REPLACEBYYOURAPP%20%28Community%29.svg)](https://ci-apps.yunohost.org/ci/apps/REPLACEBYYOURAPP/)
* TODO ARMv8-A - [![Build Status](https://ci-apps-arm.yunohost.org/ci/logs/REPLACEBYYOURAPP%20%28Community%29.svg)](https://ci-apps-arm.yunohost.org/ci/apps/REPLACEBYYOURAPP/)
* TODO Jessie x86-64b - [![Build Status](https://ci-stretch.nohost.me/ci/logs/REPLACEBYYOURAPP%20%28Community%29.svg)](https://ci-stretch.nohost.me/ci/apps/REPLACEBYYOURAPP/)

## sur LIME2 (Ynh 3.2.1, sur carte microSD)
_(les essais portent sur install,backup,remove,restore)_

- OK sauf que le /install.php de Noalyss fait un 504 Gateway Time-out nginx alors que l'installation des table postgres est toujours en cours (après ±2 ou 3min)
  - mais un refresh de /install.php affiche la page indiquant la fin d'installation et l'invite à supprimer l'install.php
  - idem lorsqu'on crée un dossier comptable … sans le 504 mais renvoie une page blanche
    - le refresh indique que le dossier existe déjà et il est fonctionnel mais j'ai attendu que les process postgress finissent leur travail
    - pas de problème pour créer un user, ni se connecter
  - donc en gros le **packaging fonctionne mais c'est nginx/php-fpm/postgress qui souffrent sur cette petite config**.

## Sur VM 32bit  avec Yunohost 3.4.2.4
_(les essais portent sur install,backup,remove,restore)_

- OK

## Sur VM 64bit  avec Yunohost 3.4.2.4
_(les essais portent sur install,backup,remove,restore)_

- OK

# Questions et Todos

## Emails

- comment fonctionnent les mails avec Noalyss ?

## PDF ?
voir : http://wiki.noalyss.eu/doku.php?id=installation:installation_sous_linux
- Pas de conversion en PDF (extension facturation et listing)
- Pas d'export en PDF des opérations de la comptabilité analytique avec les documents attachés

## Quid des mises à jours ?

Pour Nextcloud par exemple il y a un dossier `upgrade.d` dans `scripts`.  Mais si je ne me trompe, pour Noalyss il « suffit » de remplacer le dossier des sources.

## À propos de la [LICENSE](./LICENSE)

- est-ce que reprendre la GDL de Noalyss est correcte ?

## Todos

- demander à Dany de fournir un checksum pour les sources téléchargées.

# Base de travail

[example_ynh](https://github.com/YunoHost/example_ynh) a été utilisé comme source(s) d'inspiration.

## Les helpers de YunoHost

Disponibles dans `/data/helpers.d` de [Yunohost](https://github.com/YunoHost/yunohost/)

- J'ai utilisé le `psql` pour en faire une copie en tant que `_psql.sh` pour pouvoir créer de user noalyss de postgress avec les droits de createdb.

# Les sources de Noalyss

Il faut les sources de Noalyss et différentes versions sont disponibles. Actuellement les sources sont téléchargées lors de l'installation.

## Version actuelle 7.0.15 (7015)
- http://download.noalyss.eu/version-7.0/
- http://download.noalyss.eu/version-7.0/noalyss-7015.tar.gz

## Version antérieure
- http://download.noalyss.eu/version-6.9/
- http://download.noalyss.eu/version-6.9/noalyss-6.9.1.9.tar.gz
- http://download.noalyss.eu/version-6.9/noalyss-6.9.2.0.tar.gz


