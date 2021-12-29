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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
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

            return $this->redirectToRoute('show_request');
        }

        return $this->renderForm('v_request/index.html.twig', [
            'form' => $form,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ]);
    }

    #[Route('/v/request/{id}', name: 'show_request', methods: 'GET')]
    public function show(int $id): Response
    {
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
            'reason' => ($vRequest->getReason()) ? $vRequest->getReason() : 'please wait for a response',
            'createdAt' => $vRequest->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifiedAt' => ($vRequest->getModifiedAt()) ? $vRequest->getModifiedAt()->format('Y-m-d H:i:s') : null,
        ];

        if (!$vRequest) {
            return $this->json(['status' => 'No request made yet'], Response::HTTP_OK);
        }
        return $this->render('v_request/show.html.twig', [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            // 'v_request' => $serializer->serialize($data, 'json', [
            //     'circular_reference_handler' => function ($object) {
            //         return $object->getId();
            //     }
            // ]),
            'idImage' => $data['idImage'],
            'message' => $data['message'],
            'status' => $data['status'],
            'reason' => $data['reason'],
            'createdAt' => $data['createdAt'],
            'modifiedAt' => $data['modifiedAt'],
        ]);
    }

    #[Route('/v/requests', name: 'get_requests', methods: 'GET')]
    public function showAll(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normilizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normilizers, $encoders);

        $vRequests = $this->vRequestRepository->findAll();
        $data = [];

        foreach ($vRequests as $vRequest) {
            $data[] = [
                'id' => $vRequest->getId(),
                'lastname' => $vRequest->getUser()->getLastname(),
                'firstname' => $vRequest->getUser()->getFirstname(),
                'idImage' => $vRequest->getIdImage(),
                'message' => $vRequest->getMessage(),
                'status' => $vRequest->getStatus()->getName(),
                'reason' => ($vRequest->getReason()) ? $vRequest->getReason() : 'please wait for a response',
                'createdAt' => $vRequest->getCreatedAt()->format('Y-m-d H:i:s'),
                'modifiedAt' => ($vRequest->getModifiedAt()) ? $vRequest->getModifiedAt()->format('Y-m-d H:i:s') : null,
            ];
        }

        return $this->render('v_request/show_all.html.twig', [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'data'=> $data,
        ]);
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
