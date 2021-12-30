<?php

namespace App\Controller;

use App\Entity\VRequest;
use App\Form\VResponseType;
use App\Repository\VRequestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// #[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private $vRequestRepository;

    public function __construct(VRequestRepository $vRequestRepository)
    {
        $this->vRequestRepository = $vRequestRepository;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/respond/{id}', name: 'respond')]
    public function respond(Request $request, int $id): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vRequestObj = $this->vRequestRepository->findOneBy(['id'=>$id]);

        $data = [
            'id' => $vRequestObj->getId(),
            'lastname' => $vRequestObj->getUser()->getLastname(),
            'firstname' => $vRequestObj->getUser()->getFirstname(),
            'email' => $vRequestObj->getUser()->getEmail(),
            'idImage' => $vRequestObj->getIdImage(),
            'message' => $vRequestObj->getMessage(),
            'status' => $vRequestObj->getStatus()->getName(),
            'reason' => $vRequestObj->getReason(),
            'createdAt' => $vRequestObj->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifiedAt' => ($vRequestObj->getModifiedAt()) ? $vRequestObj->getModifiedAt()->format('Y-m-d H:i:s') : null,
        ];
        if (!$vRequestObj) {
            return throw $this->createNotFoundException('No request made yet');
        }

        $vRequest = new VRequest();

        $form = $this->createForm(VResponseType::class, $vRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $response = $form->get('response')->getData();
            $status = $form->get('status')->getData();

            if ($response) {
                $vRequest->setReason($response);
            }
            if ($status) {
                $vRequest->setStatus($status);
            }
            $this->vRequestRepository->respond($response, $status, $vRequestObj->getUser()->getId());

            $this->addFlash(
                'notice',
                'Response sent'
            );

            return $this->redirectToRoute('get_requests');
        }

        return $this->renderForm('admin/respond.html.twig', [
            'form' => $form,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'vr_firstname' => $data['firstname'],
            'vr_lastname' => $data['lastname'],
            'email' => $data['email'],
            'idImage' => $data['idImage'],
            'message' => $data['message'],
            'status' => $data['status'],
            'reason' => $data['reason'],
            'createdAt' => $data['createdAt'],
            'modifiedAt' => $data['modifiedAt'],
        ]);
    }

    public function sendMail()
    {
    }
}
