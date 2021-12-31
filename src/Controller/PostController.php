<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class PostController extends AbstractController
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    #[Route('/posts', name: 'posts', methods: "GET")]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $posts = $this->postRepository->findAll();
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

        return $this->render('post/index.html.twig', [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'posts' => $data,
        ]);
    }

    #[Route('/post', name: 'create_post', methods: ["GET", "POST"])]
    public function create(Request $request, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form->get('title')->getData();
            $content = $form->get('content')->getData();

            if ($title && $content) {
                $post->setTitle($title);
                $post->setContent($content);

                $post->setUser($user);
                $post->setSlug($slugger->slug(strtolower($title . ' published ' . date('now'))));
                $post->setCreatedAt();
            }

            $this->api_create($request, $slugger, $post);

            return $this->redirectToRoute('posts');
        }

        return $this->renderForm('post/create.html.twig', [
            'controller_name' => 'Create',
            'form' => $form,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ]);
    }

    #[Route('/post/{id}', name: 'show_post', methods: "GET")]
    public function show(int $id): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $post = $this->postRepository->findOneBy(['id' => $id]);

        $data = $post->toArray();

        return $this->render('post/single.html.twig', [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'post' => $data
        ]);
    }

    #[Route('/post/{slug}', name: 'show_s_post', methods: "GET")]
    public function showBySlug(string $slug): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $post = $this->postRepository->findOneBy(['slug' => $slug]);

        $data = $post->toArray();

        return $this->render('post/single.html.twig', [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'post' => $data
        ]);
    }

    #[Route('/post/edit/{id}', name: 'update_post')]
    public function update(int $id, Request $request, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $post = $this->postRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form->get('title')->getData();
            $content = $form->get('content')->getData();

            if ($title) {
                $post->setTitle($title);
                $post->setSlug($slugger->slug(strtolower($title . ' published ' . date('now'))));
            }

            if ($content) {
                $post->setContent($content);
            }
            $post->setModifiedAt();

            $this->api_update($id, $request);

            return $this->redirectToRoute('posts');
        }

        return $this->renderForm('post/create.html.twig', [
            'controller_name' => 'Update',
            'form' => $form,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ]);
    }

    #[Route('/post/delete/{id}', name: 'delete_post')]
    public function delete(int $id): Response
    {
        $this->api_delete($id);

        return $this->redirectToRoute('posts');
    }

    #[Route('/post', name: 'api_create_post', methods: "POST")]
    public function api_create(Request $request, SluggerInterface $slugger, Post $post): Response
    {
        if (!empty($post->getUser()) && !empty($post->getTitle()) && !empty($post->getSlug()) && !empty($post->getContent()) && !empty($post->getCreatedAt())) {
            $this->postRepository->create($post);

            return new Response('Post created', Response::HTTP_CREATED);
        } else {
            $data = json_decode($request->getContent(), true);

            $api_user = $data['user'];
            $api_title = $data['title'];
            $api_content = $data['content'];

            $newPost = new Post();

            $newPost->setUser($api_user);
            $newPost->setTitle($api_title);
            $post->setSlug($slugger->slug(strtolower($api_title . ' published ' . date('now'))));
            $newPost->setContent($api_content);
            $newPost->setCreatedAt();

            if (empty($api_user) || empty($api_title) || empty($api_slug) || empty($api_content) || empty($api_createdAt)) {
                throw new NotFoundHttpException('Expecting mandatory parameters!');
            }
            $this->postRepository->create($newPost);

            return new Response('Post created', Response::HTTP_CREATED);
        }
    }

    #[Route('/post/{id}', name: 'api_update_post', methods: "PUT")]
    public function api_update(int $id, Request $request): Response
    {
        $post = $this->postRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['title']) ? true : $post->setTitle($data['title']);
        empty($data['content']) ? true : $post->setContent($data['content']);

        $updated = $this->postRepository->update($post);

        return $this->json($updated->toArray(), Response::HTTP_OK);
    }

    #[Route('/post/{id}', name: 'api_delete_post', methods: "DELETE")]
    public function api_delete(int $id): Response
    {
        $post = $this->postRepository->findOneBy(['id' => $id]);

        $this->postRepository->delete($post);

        return new Response('Post Deleted!', Response::HTTP_NO_CONTENT);
    }
}
