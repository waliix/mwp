<?php
use Roots\WPConfig\Config;
Config::define('WP_ALLOW_MULTISITE', getenv('MWP_MULTISITE'));
if (env('MWP_DOMAIN')) {
    Config::define('MULTISITE', true);
    Config::define('SUBDOMAIN_INSTALL', true);
    Config::define('DOMAIN_CURRENT_SITE', getenv('MWP_DOMAIN'));
    Config::define('PATH_CURRENT_SITE', '/');
    Config::define('SITE_ID_CURRENT_SITE', 1);
    Config::define('BLOG_ID_CURRENT_SITE', 1);
    Config::define('SUNRISE', true);
}
Config::apply();
