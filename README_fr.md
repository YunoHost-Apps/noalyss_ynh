## Noalyss pour Yunohost

[![Integration level](https://dash.yunohost.org/integration/noalyss.svg)](https://dash.yunohost.org/appci/app/noalyss) ![](https://ci-apps.yunohost.org/ci/badges/noalyss.status.svg) ![](https://ci-apps.yunohost.org/ci/badges/noalyss.maintain.svg)  
[![Installer noalyss avec YunoHost](https://install-app.yunohost.org/install-with-yunohost.png)](https://install-app.yunohost.org/?app=noalyss)

*[Read this readme in english.](./README.md)*

> *Ce package vous permet d'installer Noalyss rapidement et simplement sur un serveur Yunohost.  
Si vous n'avez pas YunoHost, regardez [ici](https://yunohost.org/#/install) pour savoir comment l'installer et en profiter.*

### Vue d'ensemble

[Noalyss](http://noalyss.eu) est une application de comptabilité belge et française que vous pourrez modifier suivant vos envies. L'interface est disponible en anglais, français et néerlandais.

**Version incluse:** 8.0

### Captures d'écran

![](https://framalibre.org/sites/default/files/S%C3%A9lection_099_0.png)

### Démo

* [Démo officielle](http://demo.noalyss.eu/index.php)

### Configuration

À la fin de l'installation de l'application il faut se rendre sur `https://domaine/noalysse/install.php` pour terminer la procédure.

### Documentation

 * Documentation officielle : https://wiki.noalyss.eu/doku.php
 * Documentation YunoHost :

### Caractéristiques spécifiques YunoHost

##### Support multi-utilisateurs

* Pas d'authentification LDAP, les utilisateur·ice·s sont gérés dans l'application.
* Plusieurs utilisateur·ice·s sont donc possibles et disponibles pour les différents dossiers comptables. Une gestion des accès est prise en charge dans Noalyss.

##### Architectures supportées

* x86-64 - [![Build Status](https://ci-apps.yunohost.org/ci/logs/noalyss%20%28Community%29.svg)](https://ci-apps.yunohost.org/ci/apps/noalyss/)
* ARMv8-A - [![Build Status](https://ci-apps-arm.yunohost.org/ci/logs/noalyss%20%28Community%29.svg)](https://ci-apps-arm.yunohost.org/ci/apps/noalyss/)

##### sur LIME2 (Ynh 3.2.1, sur carte microSD)
_(les essais portent sur install,backup,remove,restore)_

- OK sauf que le /install.php de Noalyss fait un 504 Gateway Time-out nginx alors que l'installation des table postgres est toujours en cours (après ±2 ou 3min)
  - mais un refresh de /install.php affiche la page indiquant la fin d'installation et l'invite à supprimer l'install.php
  - idem lorsqu'on crée un dossier comptable … sans le 504 mais renvoie une page blanche
    - le refresh indique que le dossier existe déjà et il est fonctionnel mais j'ai attendu que les process postgress finissent leur travail
    - pas de problème pour créer un user, ni se connecter
  - donc en gros le **packaging fonctionne mais c'est nginx/php-fpm/postgress qui souffrent sur cette petite config**.

##### Sur VM 32bit avec Yunohost 3.6.5.3
_(les essais portent sur install,backup,remove,restore)_

- OK

##### Sur VM 64bit avec Yunohost 3.6.5.3
_(les essais portent sur install,backup,remove,restore)_

- OK

### Questions et Todos

##### Emails

- comment fonctionnent les mails avec Noalyss ?

##### PDF ?
voir : http://wiki.noalyss.eu/doku.php?id=installation:installation_sous_linux
- Pas de conversion en PDF (extension facturation et listing)
- Pas d'export en PDF des opérations de la comptabilité analytique avec les documents attachés

##### Quid des mises à jours ?

Pour Nextcloud par exemple il y a un dossier `upgrade.d` dans `scripts`.  Mais si je ne me trompe, pour Noalyss il « suffit » de remplacer le dossier des sources.

##### À propos de la [LICENSE](./LICENSE)

- est-ce que reprendre la GPL de Noalyss est correcte ?

### Base de travail

[example_ynh](https://github.com/YunoHost/example_ynh) a été utilisé comme source(s) d'inspiration.

##### Les helpers de YunoHost

Disponibles dans `/data/helpers.d` de [Yunohost](https://github.com/YunoHost/yunohost/)

- J'ai utilisé le `psql` pour en faire une copie en tant que `_psql.sh` pour pouvoir créer de user noalyss de postgress avec les droits de createdb.

### Les sources de Noalyss

Il faut les sources de Noalyss et différentes versions sont disponibles. Actuellement les sources sont téléchargées lors de l'installation.

- [Version actuelle 7.2](http://download.noalyss.eu/derniere-version/)
- [Versions antérieures](http://download.noalyss.eu/noalyss-package/)

## Limitations

* Limitations connues.

## Informations additionnelles

* Autres informations que vous souhaitez ajouter sur cette application.

## Liens

 * Signaler un bug : https://github.com/YunoHost-Apps/REPLACEBYYOURAPP_ynh/issues
 * Site de l'application : https://www.noalyss.eu/
 * Dépôt de l'application principale : Lien vers le dépôt officiel de l'application principale.
 * Site web YunoHost : https://yunohost.org/

---

## Informations pour les développeurs

Merci de faire vos pull request sur la [branche testing](https://github.com/YunoHost-Apps/noalyss_ynh/tree/testing).

Pour essayer la branche testing, procédez comme suit.
```
sudo yunohost app install https://github.com/YunoHost-Apps/noalyss_ynh/tree/testing --debug
ou
sudo yunohost app upgrade noalyss -u https://github.com/YunoHost-Apps/noalyss_ynh/tree/testing --debug
```
