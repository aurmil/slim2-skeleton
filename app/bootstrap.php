<?php

// ========== PHP (static) ==========

// errors

error_reporting(-1);

// charset

ini_set('default_charset', 'UTF-8');

if (5 === PHP_MAJOR_VERSION && PHP_MINOR_VERSION < 6) {
    iconv_set_encoding('internal_encoding', 'UTF-8');
    ini_set('mbstring.internal_encoding', 'UTF-8');
}

// ========== PATHS ==========

define('ROOT_PATH',   dirname(__DIR__));
define('APP_PATH',    ROOT_PATH.'/app');
define('VAR_PATH',    ROOT_PATH.'/var');

// ========== COMPOSER ==========

require ROOT_PATH.'/vendor/autoload.php';

// ========== CONFIGURATION ==========

$yaml = APP_PATH.'/config.yml';

if (file_exists($yaml) && is_file($yaml) && is_readable($yaml)) {
    $env = getenv('ENVIRONMENT') ?: 'development';

    $configCache = VAR_PATH."/cache/config/$env.json";

    if (file_exists($configCache) && is_file($configCache)
        && is_readable($configCache)
        && filemtime($configCache) > filemtime($yaml)
    ) {
        $config = json_decode(file_get_contents($configCache), true);
    } else {
        $yaml = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($yaml));

        if (isset($yaml[$env])) {
            $config = $yaml[$env];
            unset($yaml);

            $config['Slim']['mode'] = $env;

            // log

            if (isset($config['Slim']['log.level'])) {
                $config['Slim']['log.level'] = '\Slim\Log::'
                                             .$config['Slim']['log.level'];

                if (defined($config['Slim']['log.level'])) {
                    $config['Slim']['log.level'] = constant($config['Slim']['log.level']);
                } else {
                    throw new \Exception('Log level is incorrect.');
                }
            }

            // view

            $config['Slim']['templates.path'] = APP_PATH.'/templates';

            // Twig

            if (isset($config['Twig']['cache'])
                && true === $config['Twig']['cache']
            ) {
                $config['Twig']['cache'] = VAR_PATH.'/cache/twig';
            }

            // save config cache file

            file_put_contents($configCache, json_encode($config));
        } else {
            throw new \Exception("Environment $env not found in application configuration file.");
        }
    }

    // objects cannot be json-serialized

    $config['Slim']['log.writer'] = new \Slim\Logger\DateTimeFileWriter([
        'path' => VAR_PATH.'/log/app',
        'name_format' => 'Y-m',
    ]);

    $view = new \Slim\Views\Twig();
    $view->parserOptions = $config['Twig'];
    $view->parserExtensions = [new \Slim\Views\TwigExtension()];
    $config['Slim']['view'] = $view;
} else {
    throw new \Exception('Application configuration file is missing.');
}

// ========== PHP (from configuration) ==========

// time zone

date_default_timezone_set($config['PHP']['default_timezone']);

// errors & log

ini_set('display_errors',         $config['PHP']['display_errors']);
ini_set('display_startup_errors', $config['PHP']['display_startup_errors']);
ini_set('log_errors',             $config['PHP']['log_errors']);
ini_set('error_log',              VAR_PATH.'/log/php/'.date('Y-m').'.log');

// session

if (true === $config['PHP']['need_session']) {
    session_cache_limiter(false);
    session_set_cookie_params(0, '/', '', $config['PHP']['session_cookie_secure'], true);
    session_start();
}

// ========== SLIM ==========

// init

$app = new \Slim\Slim($config['Slim']);

$app->config('app', $config['App']);
$app->view()->setData('config', $app->config('app'));

// routes

$app->get('/', '\App\Controller\Front:home')
    ->name('home')
    ->setParams([$app]);

// errors

$app->notFound(function () use ($app) {
    $app->render('not-found.twig');
});

$app->error(function (\Exception $e) use ($app) {
    $app->getLog()->error($e);
    $app->render('error.twig');
});

// dispatch

$app->run();
