# Noalyss for YunoHost

[![Integration level](https://dash.yunohost.org/integration/noalyss.svg)](https://dash.yunohost.org/appci/app/noalyss) ![](https://ci-apps.yunohost.org/ci/badges/noalyss.status.svg) ![](https://ci-apps.yunohost.org/ci/badges/noalyss.maintain.svg)  
[![Install Noalyss with YunoHost](https://install-app.yunohost.org/install-with-yunohost.svg)](https://install-app.yunohost.org/?app=noalyss)

*[Lire ce readme en français.](./README_fr.md)*

> *This package allow you to install Noalyss quickly and simply on a YunoHost server.  
If you don't have YunoHost, please see [here](https://yunohost.org/#/install) to know how to install and enjoy it.*

## Overview
[Noalyss](http://noalyss.eu) is a belgian and french accountant software available in english, dutch and french.

**Included version:** 8.1.0.1

## Screenshots

![](https://framalibre.org/sites/default/files/S%C3%A9lection_099_0.png)

## Demo

* [Official demo](http://demo.noalyss.eu/index.php)

## Configuration

At the end of the installation process, you need to open `https://domain/noalyss/install.php` to start using it.

## Documentation

 * Official documentation: https://wiki.noalyss.eu/doku.php

## YunoHost specific features

#### Multi-users support

* There is no LDAP integration. Users are handeled within the application.
* Multiple users and accountant books is possible. Access rights are done within Noalyss.

#### Supported architectures

* x86-64 - [![Build Status](https://ci-apps.yunohost.org/ci/logs/noalyss%20%28Community%29.svg)](https://ci-apps.yunohost.org/ci/apps/noalyss/)
* ARMv8-A - [![Build Status](https://ci-apps-arm.yunohost.org/ci/logs/noalyss%20%28Community%29.svg)](https://ci-apps-arm.yunohost.org/ci/apps/noalyss/)

## Limitations

* Any known limitations.

## Additional information

* Other info you would like to add about this app.

## Links

 * Report a bug: https://github.com/YunoHost-Apps/noalyss_ynh/issues
 * App website: https://www.noalyss.eu/
 * Upstream app repository: Link to the official repository of the upstream app.
 * YunoHost website: https://yunohost.org/

---

## Developer info

Please send your pull request to the [testing branch](https://github.com/YunoHost-Apps/noalyss_ynh/tree/testing).

To try the testing branch, please proceed like that.
```
sudo yunohost app install https://github.com/YunoHost-Apps/noalyss_ynh/tree/testing --debug
or
sudo yunohost app upgrade noalyss -u https://github.com/YunoHost-Apps/noalyss_ynh/tree/testing --debug
```
