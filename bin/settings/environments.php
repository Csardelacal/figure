<?php

use spitfire\core\Environment;

/*
 * Creates a test environment that can be used to store configuration that affects
 * the behavior of an application.
 */
$e = new Environment('test');
$e->set('db', 'mysqlpdo://root:root@localhost/database?prefix=figure_&encoding=utf8');
$e->set('SSO', 'http://1167085183:I1mykN6nwLyPd1eiUcBgEYMQj7SUD5iuVWVavn9CQIZ5lY@localhost/Auth');
$e->set('uploads.directory', 'cloudy://h45jk56/');
$e->set('storage.engines.cloudy', '\cloudy\sf\Mount:http://1539873937@localhost/cloudy/pool1|http://1167085183:I1mykN6nwLyPd1eiUcBgEYMQj7SUD5iuVWVavn9CQIZ5lY@localhost/Auth');
$e->set('memcached_enabled', true);

$e->set('assets.directory.deploy', '/assets/deploy/2020-07-08-17-57/');