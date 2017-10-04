# QUOTA PLUGIN FOR ROUNDCUBE

## What is Quota plugin ?

Quota plugin is used in [Roundcube](https://roundcube.net/) to show
*used space* and *free space* for given mailbox.

![](doc/screenshot/1.png)


## How to install this plugin in Rouncube ?

1. Create folder `quota` in `ROUNDCUBE_HOME/plugins` if it does not exist.
2. Copy all plugin files there.
3. Copy `config.example.php` to `config.php` and edit `config.php` if you want.
```php
<?php

return array(

    // enable debug mode for development?
    'debug' => false,
    // admin contact information
    'admin_contact' => '<a href="mailto:opr@ee.nthu.edu.tw">opr@ee.nthu.edu.tw</a>',
    // show the admin contact information on the plugin page
    'show_admin_contact' => false,

    // enable chart presentation
    'enable_chart_presentation' => true,
    // enable text presentation
    'enable_text_presentation' => true,

);
```
4. Edit `ROUNDCUBE_HOME/conf/config.inc.php` locate `$config['plugins']` and add `'quota',` there:
```php
<?php

// some other codes...

$config['plugins'] = array(
    // some other plugins...
    'quota', // <-- add this
);
```


## How to set mailbox quota in Dovecot ?

```bash
sudo nano /etc/dovecot/conf.d/90-quota.conf

	plugin {
		quota = maildir:User quota
		quota_rule = *storage=900M
		quota_rule2 = Trash:storage=+100M
		...
	}

sudo service dovecot restart
```


## How to set mailbox quota in Postfix ?

:octocat: Victor Kirov
