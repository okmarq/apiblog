<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(#[CurrentUser] User $user = null): Response
    {
        return $this->render('home/index.html.twig', [
            'user' => 'Reader',
            'firstname' => ($user) ? $user->getfirstname() : '',
            'lastname' => ($user) ? $user->getLastname() : ''
        ]);
    }
}
