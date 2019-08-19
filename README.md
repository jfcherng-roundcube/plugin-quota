# Roundcube Plugin: Quota

<a href="https://plugins.roundcube.net/packages/jfcherng/quota"><img alt="Roundcube Plugins" src="https://img.shields.io/badge/dynamic/json?label=downloads&query=$.package.downloads.total&url=https://plugins.roundcube.net/packages/jfcherng/quota.json"></a>
<a href="https://github.com/jfcherng/roundcube-plugin-quota/tags"><img alt="GitHub tag (latest SemVer)" src="https://img.shields.io/github/tag/jfcherng/roundcube-plugin-quota?logo=github"></a>
<a href="https://github.com/jfcherng/roundcube-plugin-quota/blob/master/LICENSE"><img alt="Project license" src="https://img.shields.io/github/license/jfcherng/roundcube-plugin-quota"></a>
<a href="https://github.com/jfcherng/roundcube-plugin-quota/stargazers"><img alt="GitHub stars" src="https://img.shields.io/github/stars/jfcherng/roundcube-plugin-quota?logo=github"></a>
<a href="https://www.paypal.me/jfcherng/5usd" title="Donate to this project using Paypal"><img src="https://img.shields.io/badge/paypal-donate-blue.svg?logo=paypal" /></a>

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

This plugin has been published on [the official Roundcube plugin repository](https://plugins.roundcube.net) by the name of [jfcherng/quota](https://plugins.roundcube.net/packages/jfcherng/quota).

1. Go to your `ROUNDCUBE_HOME` (i.e., the root directory of your Roundcube).
2. Run `$ composer require jfcherng/quota`.
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


## How to set mailbox quota in Postfix?

*Feel free to finish this section by submitting a Pull Request.*
