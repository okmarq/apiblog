<?php

namespace App\Controller;

use App\Entity\VRequest;

use App\Form\VRequestType;
use App\Repository\VRequestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class VRequestController extends AbstractController
{
    private $vRequestRepository;

    public function __construct(VRequestRepository $vRequestRepository)
    {
        $this->vRequestRepository = $vRequestRepository;
    }

    #[Route('/v/request/{id}', name: 'show_request', methods: 'GET')]
    public function index(int $id): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vRequest = $this->vRequestRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $vRequest->getId(),
            'idImage' => $vRequest->getIdImage(),
            'message' => $vRequest->getMessage(),
            'status' => $vRequest->getStatus()->getName(),
            'role' => implode(', ', $vRequest->getUser()->getRoles()),
            'vr_firstname' => $vRequest->getUser()->getFirstname(),
            'vr_lastname' => $vRequest->getUser()->getLastname(),
            'email' => $vRequest->getUser()->getEmail(),
            'reason' => ($vRequest->getReason()) ? $vRequest->getReason() : 'please wait for a response',
            'createdAt' => $vRequest->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifiedAt' => ($vRequest->getModifiedAt()) ? $vRequest->getModifiedAt()->format('Y-m-d H:i:s') : null,
        ];

        if (!$vRequest) {
            return $this->json(['status' => 'No request made yet']);
        }

        return $this->render('v_request/show.html.twig', [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'vr_firstname'=> $data['vr_firstname'],
            'vr_lastname'=> $data['vr_lastname'],
            'email'=> $data['email'],
            'id' => $data['id'],
            'idImage' => $data['idImage'],
            'message' => $data['message'],
            'status' => $data['status'],
            'role' => $data['role'],
            'reason' => $data['reason'],
            'createdAt' => $data['createdAt'],
            'modifiedAt' => $data['modifiedAt'],
        ]);
    }

    #[Route('/v/request', name: 'v_request', methods: ["POST", 'GET'])]
    public function create(Request $request, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $requestId = $user->getVRequest() ? $user->getVRequest()->getId() : null;
        
        $vRequest = $this->vRequestRepository->findOneBy(['id' => $requestId]);
        $data = [];

        if (!$vRequest) {
            $data[] = ['status' => 'none'];
        } else {
            $data[] = [
                'id' => $vRequest->getId(),
                'vr_firstname' => $vRequest->getUser()->getFirstname(),
                'vr_lastname' => $vRequest->getUser()->getLastname(),
                'email' => $vRequest->getUser()->getEmail(),
                'role' => implode(', ', $vRequest->getUser()->getRoles()),
                'idImage' => $vRequest->getIdImage(),
                'message' => $vRequest->getMessage(),
                'status' => $vRequest->getStatus()->getName(),
                'reason' => ($vRequest->getReason()) ? $vRequest->getReason() : 'please wait for a response',
                'createdAt' => $vRequest->getCreatedAt()->format('Y-m-d H:i:s'),
                'modifiedAt' => ($vRequest->getModifiedAt()) ? $vRequest->getModifiedAt()->format('Y-m-d H:i:s') : null,
            ];
        }

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

            $res = $this->vRequestRepository->create($newImageName, $message, $user->getId());

            $this->addFlash(
                'notice',
                'Request received. Response will be sent to ' . $user->getEmail()
            );

            return $this->redirectToRoute('show_request', ['id' => $res->getId()]);
        }

        return $this->renderForm('v_request/index.html.twig', [
            'controller_name' => 'Create',
            'form' => $form,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'data' => $data,
        ]);
    }

    #[Route("/v/request/edit/{id}", name: "update_request")]
    public function update(int $id, Request $request, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vRequest = $this->vRequestRepository->findOneBy(['id' => $id]);

        $data = [];

        if (!$vRequest) {
            $data[] = ['status' => 'none'];
        } else {
            $data[] = [
                'id' => $vRequest->getId(),
                'firstname' => $vRequest->getUser()->getFirstname(),
                'lastname' => $vRequest->getUser()->getLastname(),
                'role' => implode(', ', $vRequest->getUser()->getRoles()),
                'idImage' => $vRequest->getIdImage(),
                'message' => $vRequest->getMessage(),
                'status' => $vRequest->getStatus()->getName(),
                'reason' => ($vRequest->getReason()) ? $vRequest->getReason() : 'please wait for a response',
                'createdAt' => $vRequest->getCreatedAt()->format('Y-m-d H:i:s'),
                'modifiedAt' => ($vRequest->getModifiedAt()) ? $vRequest->getModifiedAt()->format('Y-m-d H:i:s') : null,
            ];
        }

        $vRequest->setIdImage(new File($this->getParameter('images_directory') . '/' . $vRequest->getIdImage()));

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

            $this->vRequestRepository->update($vRequest);

            return $this->redirectToRoute('dashboard');
        }

        return $this->renderForm('v_request/index.html.twig', [
            'controller_name' => 'Update',
            'form' => $form,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'data' => $data,
        ]);
    }

    #[Route("/v/request/delete/{id}", name: "delete_request", methods: "DELETE")]
    public function delete(int $id, Request $request): Response
    {
        $vRequest = $this->vRequestRepository->findOneBy(['id' => $id]);

        $this->vRequestRepository->remove($vRequest);

        return $this->json(['status' => 'Request deleted'], Response::HTTP_NO_CONTENT);
    }
}
