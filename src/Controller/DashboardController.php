<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\VRequestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class DashboardController extends AbstractController
{
    private $postRepository;
    private $vRequestRepository;

    public function __construct(PostRepository $postRepository, VRequestRepository $vRequestRepository)
    {
        $this->postRepository = $postRepository;
        $this->vRequestRepository = $vRequestRepository;
    }

    #[Route('/dashboard', name: 'dashboard', methods: "GET")]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $posts = $this->postRepository->findAll();
        $vRequest = $this->vRequestRepository->findOneBy(['id' => $user->getId()]);
        $data = [];

        foreach ($posts as $post) {
            $data[] = [
                'title' => $post->getTitle(),
                'slug' => $post->getSlug(),
                'content' => $post->getContent(),
                'id' => $post->getId(),
                'lastname' => $post->getUser()->getLastname(),
                'firstname' => $post->getUser()->getFirstname(),
                'createdAt' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
                'modifiedAt' => ($post->getModifiedAt()) ? $post->getModifiedAt()->format('Y-m-d H:i:s') : null,
            ];
        }
        if ($vRequest) {
            $v_request = [
                'id' => $vRequest->getId(),
                'firstname' => $vRequest->getUser()->getFirstname(),
                'lastname' => $vRequest->getUser()->getLastname(),
                'role' => implode(', ', $vRequest->getUser()->getRoles()),
                'email' => $vRequest->getUser()->getEmail(),
                'idImage' => $vRequest->getIdImage(),
                'message' => $vRequest->getMessage(),
                'status' => $vRequest->getStatus()->getName(),
                'reason' => $vRequest->getReason(),
                'createdAt' => $vRequest->getCreatedAt()->format('Y-m-d H:i:s'),
                'modifiedAt' => ($vRequest->getModifiedAt()) ? $vRequest->getModifiedAt()->format('Y-m-d H:i:s') : null,
            ];
        } else {
            $v_request = ['status' => 'none'];
        }

        return $this->render('dashboard/index.html.twig', [
            'firstname' => $user->getfirstname(),
            'lastname' => $user->getLastname(),
            'posts' => $data,
            'v_request' => $v_request
        ]);
    }
}
