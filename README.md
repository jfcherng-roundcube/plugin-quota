# Roundcube Plugin: Quota

[![GitHub Workflow Status (branch)](https://img.shields.io/github/workflow/status/jfcherng-roundcube/plugin-quota/main/master?style=flat-square)](https://github.com/jfcherng-roundcube/plugin-quota/actions)
[![Packagist](https://img.shields.io/packagist/dt/jfcherng-roundcube/quota?style=flat-square)](https://packagist.org/packages/jfcherng-roundcube/quota)
[![Packagist Version](https://img.shields.io/packagist/v/jfcherng-roundcube/quota?style=flat-square)](https://packagist.org/packages/jfcherng-roundcube/quota)
[![Project license](https://img.shields.io/github/license/jfcherng-roundcube/plugin-quota?style=flat-square)](https://github.com/jfcherng-roundcube/plugin-quota/blob/master/LICENSE)
[![GitHub stars](https://img.shields.io/github/stars/jfcherng-roundcube/plugin-quota?style=flat-square&logo=github)](https://github.com/jfcherng-roundcube/plugin-quota/stargazers)
[![Donate to this project using Paypal](https://img.shields.io/badge/paypal-donate-blue.svg?style=flat-square&logo=paypal)](https://www.paypal.me/jfcherng/5usd)

A plugin that shows quota information with a pie chart for Roundcube.

## Requirements

I only test this plugin with following environments. Other setup may work with luck.

- PHP: >= `5.4.0`

## What is Quota plugin

Quota plugin is used in [Roundcube](https://roundcube.net/) to show
_used space_ and _free space_ for given mailbox.

![demo](https://raw.githubusercontent.com/jfcherng-roundcube/plugin-quota/master/docs/screenshot/demo.png)

## How to install this plugin in Roundcube

### Install via Composer

This plugin has been published on [Packagist](https://packagist.org) by the name of [jfcherng-roundcube/quota](https://packagist.org/packages/jfcherng-roundcube/quota).

1. Go to your `ROUNDCUBE_HOME` (i.e., the root directory of your Roundcube).
2. Run `composer require jfcherng-roundcube/quota`.
3. You may edit the `config.inc.php` under this plugin's directory if you want to do some configurations.

### Install manually

1. Create folder `quota` in `ROUNDCUBE_HOME/plugins` if it does not exist.
2. Copy all plugin files there.
3. Copy `config.inc.php.dist` to `config.inc.php` and edit `config.inc.php` if you want.
4. Edit `ROUNDCUBE_HOME/conf/config.inc.php` locate `$config['plugins']` and add `'quota',` there:

```php
<?php

// some other codes...

$config['plugins'] = array(
    // some other plugins...
    'quota', // <-- add this
);
```

## How to set mailbox quota in Dovecot

```bash
sudo vim /etc/dovecot/conf.d/90-quota.conf
```

```text
  plugin {
    quota = maildir:User quota
    quota_rule = *storage=900M
    quota_rule2 = Trash:storage=+100M
    ...
  }
```

```bash
sudo service dovecot restart
```

You may also refer to the official document of Dovecot's quota plugin:
https://wiki.dovecot.org/Quota/Configuration

## How to set mailbox quota in Postfix

_Feel free to finish this section by submitting a Pull Request._
