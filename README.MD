A plugin that shows quota information with a pie chart for Roundcube.


## Requirements

- PHP: >= `5.4.0`
- Roundcube: I only tested this plugin with `1.1.12`, `1.3.9` and `1.4-rc1`


## What is Quota plugin?

Quota plugin is used in [Roundcube](https://roundcube.net/) to show 
*used space* and *free space* for given mailbox.

![](https://raw.githubusercontent.com/jfcherng/roundcube-quota-plugin/master/doc/screenshot/demo.png)


## How to install this plugin in Roundcube?


### Install via Composer

This plugin has been [published](https://plugins.roundcube.net/packages/jfcherng/quota) on the official Roundcube plugin repository.

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
sudo vim /etc/dovecot/conf.d/90-quota.conf
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
sudo service dovecot restart
```


## How to set mailbox quota in Postfix?

*Feel free to finish this section and submit a Pull Request.*


Supporters <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ATXYY9Y78EQ3Y" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" /></a>
==========

Thank you guys for sending me some cups of coffee.
