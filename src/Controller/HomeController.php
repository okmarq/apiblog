<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(#[CurrentUser] User $user = null): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('home/index.html.twig', [
            'guest' => 'Reader',
            'firstname' => ($user) ? $user->getfirstname() : '',
            'lastname' => ($user) ? $user->getLastname() : ''
        ]);
    }
}
