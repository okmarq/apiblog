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
        // get request from database and display here
        $result = ['status' => 'No request made yet'];

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

            $this->addFlash(
                'notice',
                'Request received. Response will be sent to ' . $user->getEmail()
            );
        }

        return $this->renderForm('v_request/index.html.twig', [
            'form' => $form,
            'firstname' => $user->getfirstname(),
            'lastname' => $user->getLastname(),
        ]);
    }

    #[Route('/v/request/{id}', name: 'show_request')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $vRequest = $doctrine->getRepository(VRequest::class)->find($id);

        if (!$vRequest) {
            return $this->json(['status' => 'No request found'], Response::HTTP_OK);
        }
        return $this->json($vRequest, Response::HTTP_OK);
    }

    public function update()
    {
        $vRequest = new VRequest();
        $vRequest->setIdImage(
            new File($this->getParameter('images_directory') . '/' . $vRequest->getIdImage())
        );
    }
}
