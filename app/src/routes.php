<?php

// Routes

$app->get('/', 'App\Controller\Front:home')
    ->name('home')
    ->setParams([$app]);

// Page not found handler

$app->notFound(function () use ($app) {
    $app->render('errors/not-found.twig');
});
