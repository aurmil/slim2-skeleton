<?php

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
        $config = Symfony\Component\Yaml\Yaml::parse(file_get_contents($yaml));

        if (isset($config[$env])) {
            $config = $config[$env];

            // Slim

            $config['Slim']['mode'] = $env;

            // Logger

            if (isset($config['Slim']['log.level'])) {
                $level = 'Slim\Log::'.$config['Slim']['log.level'];

                if (defined($level)) {
                    $config['Slim']['log.level'] = constant($level);
                } else {
                    throw new \Exception('Slim log level is incorrect.');
                }
            }

            // View renderer

            $config['Slim']['templates.path'] = APP_PATH.'/templates';

            if (true === $config['Twig']['cache']) {
                $config['Twig']['cache'] = VAR_PATH.'/cache/twig';
            }

            // save config cache file

            file_put_contents($configCache, json_encode($config));
        } else {
            throw new \Exception("Environment $env not found in configuration file.");
        }
    }
} else {
    throw new \Exception('Configuration file not found.');
}

return $config;
