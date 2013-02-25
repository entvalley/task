<?php

$container->setParameter('database_driver', 'pdo_mysql');

if (getenv('VCAP_SERVICES') != '') {
    $services_json = json_decode(getenv("VCAP_SERVICES"),true);
    $postgresql_config = $services_json["postgresql-9.1"][0]["credentials"];

    $container->setParameter('database_host', $postgresql_config["hostname"]);
    $container->setParameter('database_port', $postgresql_config["port"]);
    $container->setParameter('database_user', $postgresql_config["username"]);
    $container->setParameter('database_password', $postgresql_config["password"]);
    $container->setParameter('database_name', $postgresql_config["name"]);
} else {
    $container->setParameter('database_host', 'localhost');
    $container->setParameter('database_port', '3306');
    $container->setParameter('database_user', 'root');
    $container->setParameter('database_password', 'nbuser');
    $container->setParameter('database_name', 'tasks');
}

$container->setParameter('mailer_transport', 'smtp');
$container->setParameter('mailer_user', null);
$container->setParameter('mailer_host', 'localhost');
$container->setParameter('mailer_password', null);
$container->setParameter('locale', 'en');
$container->setParameter('secret', 'd1535f102c4487c5d029985bc7456d2e6');
