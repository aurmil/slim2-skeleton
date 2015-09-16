<?php

namespace App\Controller;

class Front
{
    public function home($app)
    {
        $app->render('front/home.twig');
    }
}
