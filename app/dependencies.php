<?php

// Logger

$handlers = [new Monolog\Handler\StreamHandler(VAR_PATH.'/log/app-'.date('Y-m').'.log')];

if (true === $config['App']['errors']['send_email']
    && '' != $config['App']['errors']['email']
) {
    $handlers[] = new Monolog\Handler\NativeMailerHandler(
        $config['App']['errors']['email'],
        $config['App']['errors']['email_subject'] ?: 'Error',
        $config['App']['errors']['email']
    );
}

$app->getLog()->setWriter(new Flynsarmy\SlimMonolog\Log\MonologWriter([
    'handlers' => $handlers,
]));

// View renderer

$view = new Slim\Views\Twig();
$view->parserOptions = $config['Twig'];
$view->parserExtensions = [new Slim\Views\TwigExtension()];

$app->view($view);
