<?php

namespace App\Controller\Data;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ArrayManipulation extends AbstractController
{
    public function index()
    {
        return new Response("OK");

    }
}
