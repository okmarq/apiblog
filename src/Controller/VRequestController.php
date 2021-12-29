<?php

namespace App\Controller;

use App\Entity\VRequest;

use App\Form\VRequestType;
use App\Repository\VRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
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

    #[Route('/v/request/{id}', name: 'show_request', methods: 'GET')]
    public function show(int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normilizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normilizers, $encoders);

        $vRequest = $this->vRequestRepository->findOneBy(['id' => $id]);

        $data = [
            'idImage' => $vRequest->getIdImage(),
            'message' => $vRequest->getMessage(),
            'status' => $vRequest->getStatus()->getName(),
            'reason' => $vRequest->getReason(),
            // 'createdAt' => $vRequest->getCreatedAt(),
            // 'modifiedAt' => $vRequest->getModifiedAt(),
        ];

        if (!$vRequest) {
            return $this->json(['status' => 'No request made yet'], Response::HTTP_OK);
        }
        return $this->render('v_request/show.html.twig', [
            'firstname' => $user->getfirstname(),
            'lastname' => $user->getLastname(),
            'v_request' => $serializer->serialize($data, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]),
            'idImage' => $data['idImage'],
            'message' => $data['message'],
            'status' => $data['status'],
            'reason' => $data['reason'],
        ]);
    }

    #[Route('/v/requests', name: 'get_requests', methods: 'GET')]
    public function showAll(): Response
    {
        $vRequests = $this->vRequestRepository->findAll();
        $data = [];

        foreach ($vRequests as $vRequest) {
            $data = [
                'id' => $vRequest->getId(),
                'user' => $vRequest->getUser(),
                'idImage' => $vRequest->getIdImage(),
                'message' => $vRequest->getMessage(),
                'status' => $vRequest->getStatus(),
                'reason' => $vRequest->getReason(),
                'createdAt' => $vRequest->getCreatedAt(),
                'modifiedAt' => $vRequest->getModifiedAt(),
            ];
        }

        if (!$vRequest) {
            return $this->json(['status' => 'No requests found'], Response::HTTP_OK);
        }
        return $this->json($data, Response::HTTP_OK);
    }

    #[Route("/v/request/{id}", name: "update_request", methods: "PUT")]
    public function update(int $id, Request $request): Response
    {
        $vRequest = $this->vRequestRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['idImage']) ? true : $vRequest->setIdImage(
            new File($this->getParameter('images_directory') . '/' . $vRequest->getIdImage())
        );
        empty($data['message']) ? true : $vRequest->setMessage($data['message']);

        $updatedVRequest = $this->vRequestRepository->update($vRequest);

        return $this->json($updatedVRequest->toArray(), Response::HTTP_OK);
    }

    #[Route("/v/request/{id}", name: "delete_request", methods: "DELETE")]
    public function delete(int $id, Request $request): Response
    {
        $vRequest = $this->vRequestRepository->findOneBy(['id' => $id]);

        $this->vRequestRepository->remove($vRequest);

        return $this->json(['status' => 'Request deleted'], Response::HTTP_NO_CONTENT);
    }
}
