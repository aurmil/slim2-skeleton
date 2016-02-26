<?php

// Logger

$handlers = [];

$formatter = new Monolog\Formatter\LineFormatter;
$formatter->includeStacktraces();

$handler = new Monolog\Handler\StreamHandler(
    VAR_PATH.'/log/app-'.date('Y-m').'.log'
);
$handler->setFormatter($formatter);
$handlers[] = $handler;

if (true === $config['App']['errors']['send_email']
    && '' != $config['App']['errors']['email_to']
) {
    $handler = new Monolog\Handler\NativeMailerHandler(
        $config['App']['errors']['email_to'],
        $config['App']['errors']['email_subject'] ?: 'Error',
        $config['App']['errors']['email_from']
    );
    $handler->setFormatter($formatter);
    $handlers[] = $handler;
}

$app->getLog()->setWriter(new Flynsarmy\SlimMonolog\Log\MonologWriter([
    'handlers' => $handlers,
]));

// View renderer

$view = new Slim\Views\Twig();
$view->parserOptions = $config['Twig'];
$view->parserExtensions = [new Slim\Views\TwigExtension()];

$app->view($view);
