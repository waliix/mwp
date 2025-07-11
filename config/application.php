<?php
use Roots\WPConfig\Config;
$base_url = dirname(__DIR__);
$site_url = $base_url . '/public';

if (file_exists($base_url . '/.env')) {
    $env_files = file_exists($base_url . '/.env.local')
        ? ['.env', '.env.local']
        : ['.env'];
    $repository = Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()
        ->addAdapter(Dotenv\Repository\Adapter\EnvConstAdapter::class)
        ->addAdapter(Dotenv\Repository\Adapter\PutenvAdapter::class)
        ->immutable()
        ->make();
    $dotenv = Dotenv\Dotenv::create($repository, $base_url, $env_files, false);
    $dotenv->load();
    $dotenv->required(['WP_HOME', 'WP_SITEURL']);
    if (!getenv('DATABASE_URL')) {
        $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASS']);
    }
}

define('WP_ENV', getenv('WP_ENV') ?: 'production');

if (!getenv('WP_ENVIRONMENT_TYPE') && in_array(WP_ENV, ['production', 'staging', 'development', 'local'])) {
    Config::define('WP_ENVIRONMENT_TYPE', WP_ENV);
}

Config::define('WP_HOME', getenv('WP_HOME'));
Config::define('WP_SITEURL', getenv('WP_SITEURL'));

Config::define('CONTENT_DIR', '/app');
Config::define('WP_CONTENT_DIR', $site_url . Config::get('CONTENT_DIR'));
Config::define('WP_CONTENT_URL', Config::get('WP_HOME') . Config::get('CONTENT_DIR'));

if (getenv('DB_SSL')) {
    Config::define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
}

Config::define('DB_NAME', getenv('DB_NAME'));
Config::define('DB_USER', getenv('DB_USER'));
Config::define('DB_PASSWORD', getenv('DB_PASS'));
Config::define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
Config::define('DB_CHARSET', 'utf8mb4');
Config::define('DB_COLLATE', '');
$table_prefix = getenv('TB_PREFIX') ?: 'wp_';

if (getenv('DATABASE_URL')) {
    $dsn = (object) parse_url(getenv('DATABASE_URL'));

    Config::define('DB_NAME', substr($dsn->path, 1));
    Config::define('DB_USER', $dsn->user);
    Config::define('DB_PASSWORD', isset($dsn->pass) ? $dsn->pass : null);
    Config::define('DB_HOST', isset($dsn->port) ? "{$dsn->host}:{$dsn->port}" : $dsn->host);
}
// Definisi => Authentication Unique Keys and Salts
Config::define('AUTH_KEY', getenv('AUTH_KEY'));
Config::define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY'));
Config::define('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY'));
Config::define('NONCE_KEY', getenv('NONCE_KEY'));
Config::define('AUTH_SALT', getenv('AUTH_SALT'));
Config::define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
Config::define('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT'));
Config::define('NONCE_SALT', getenv('NONCE_SALT'));

Config::define('AUTOMATIC_UPDATER_DISABLED', true);
Config::define('DISABLE_WP_CRON', getenv('DISABLE_WP_CRON') ?: false);
Config::define('DISALLOW_FILE_EDIT', true);
Config::define('DISALLOW_FILE_MODS', false);
Config::define('WP_POST_REVISIONS', getenv('WP_POST_REVISIONS') ?? true);
Config::define('CONCATENATE_SCRIPTS', false);
// => Tema
Config::define('WP_DEFAULT_THEME', getenv('MWP_THEME', 'mwp'));
// => Debug
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', false);
Config::define('SCRIPT_DEBUG', false);
ini_set('display_errors', '0');

// Referensi => https://codex.wordpress.org/Function_Reference/is_ssl#Notes
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';
if (file_exists($env_config)) {
    require_once $env_config;
}

Config::apply();

// =>Folder WP
if (!defined('ABSPATH')) {
    define('ABSPATH', $site_url . '/wp/');
}
