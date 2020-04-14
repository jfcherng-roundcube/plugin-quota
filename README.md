# Roundcube Plugin: Quota

[![Codacy grade](https://img.shields.io/codacy/grade/3a7a07d2ed67434e8e8582ea4ec9867b/v6?style=flat-square)](https://app.codacy.com/project/jfcherng/roundcube-plugin-quota/dashboard)
[![Packagist](https://img.shields.io/packagist/dt/jfcherng/quota?style=flat-square)](https://packagist.org/packages/jfcherng/quota)
[![Packagist Version](https://img.shields.io/packagist/v/jfcherng/quota?style=flat-square)](https://packagist.org/packages/jfcherng/quota)
[![Project license](https://img.shields.io/github/license/jfcherng/roundcube-plugin-quota?style=flat-square)](https://github.com/jfcherng/roundcube-plugin-quota/blob/v6/LICENSE)
[![GitHub stars](https://img.shields.io/github/stars/jfcherng/roundcube-plugin-quota?style=flat-square&logo=github)](https://github.com/jfcherng/roundcube-plugin-quota/stargazers)
[![Donate to this project using Paypal](https://img.shields.io/badge/paypal-donate-blue.svg?style=flat-square&logo=paypal)](https://www.paypal.me/jfcherng/5usd)

A plugin that shows quota information with a pie chart for Roundcube.


## Requirements

I only test this plugin with following environments. Other setup may work with luck.

- PHP: >= `5.4.0`


## What is Quota plugin?

Quota plugin is used in [Roundcube](https://roundcube.net/) to show 
*used space* and *free space* for given mailbox.

![](https://raw.githubusercontent.com/jfcherng/roundcube-quota-plugin/master/doc/screenshot/demo.png)


## How to install this plugin in Roundcube?


### Install via Composer

This plugin has been published on [the official Roundcube plugin repository](https://plugins.roundcube.net) by the name of [jfcherng/roundcube-plugin-quota](https://plugins.roundcube.net/packages/jfcherng/roundcube-plugin-quota).

1. Go to your `ROUNDCUBE_HOME` (i.e., the root directory of your Roundcube).
2. Run `$ composer require jfcherng/roundcube-plugin-quota`.
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


## How to set mailbox quota in Dovecot?

```bash
$ sudo vim /etc/dovecot/conf.d/90-quota.conf
```

```
	plugin {
		quota = maildir:User quota
		quota_rule = *storage=900M
		quota_rule2 = Trash:storage=+100M
		...
	}
```

```bash
$ sudo service dovecot restart
```

You may also refer to the official document of Dovecot's quota plugin:
https://wiki.dovecot.org/Quota/Configuration


## How to set mailbox quota in Postfix?

*Feel free to finish this section by submitting a Pull Request.*
