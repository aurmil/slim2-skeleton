<?php

namespace App\Controller;

class Front
{
    public function home($app)
    {
        $app->render('home.twig');
    }
}
