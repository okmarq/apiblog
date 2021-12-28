<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VRequestController extends AbstractController
{
    #[Route('/v/request', name: 'v_request')]
    public function index(): Response
    {
        return $this->render('v_request/index.html.twig', [
            'controller_name' => 'VRequestController',
        ]);
    }
}
