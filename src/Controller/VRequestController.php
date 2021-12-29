<?php

namespace App\Controller;

use App\Entity\VRequest;

use App\Form\VRequestType;
use App\Repository\VRequestRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class VRequestController extends AbstractController
{
    private $vRequestRepository;

    public function __construct(VRequestRepository $vRequestRepository)
    {
        $this->vRequestRepository = $vRequestRepository;
    }

    #[Route('/v/request', name: 'v_request')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vRequest = new VRequest();

        $form = $this->createForm(VRequestType::class, $vRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $idImage */
            $idImage = $form->get('idImage')->getData();
            $message = $form->get('message')->getData();

            if ($idImage) {
                $originalFilename = pathinfo($idImage->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newImageName = $safeFilename . '-' . uniqid() . '.' . $idImage->guessExtension();

                try {
                    $idImage->move(
                        $this->getParameter('images_directory'),
                        $newImageName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $vRequest->setIdImage($newImageName);
            }

            if ($message) {
                $vRequest->setMessage($message);
            }

            $this->vRequestRepository->create($newImageName, $message);

            return $this->json(['status' => 'Your request has been received, response will be delivered to your email address @' . $user->getEmail()], Response::HTTP_CREATED);
        }

        return $this->renderForm('v_request/index.html.twig', [
            'form' => $form,
            'firstname' => $user->getfirstname(),
            'lastname' => $user->getLastname(),
            'status' => $user->getLastname(),
        ]);
    }

    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        return $this->json([], Response::HTTP_OK);
    }

    public function update()
    {
        $vRequest = new VRequest();
        $vRequest->setIdImage(
            new File($this->getParameter('images_directory') . '/' . $vRequest->getIdImage())
        );
    }
}
