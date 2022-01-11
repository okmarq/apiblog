<?php

namespace App\Controller;

use App\Form\VResponseType;
use App\Repository\RoleRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use App\Repository\VRequestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private $vRequestRepository;
    private $userRepository;
    private $statusRepository;
    private $roleRepository;
    private $mailer;

    public function __construct(VRequestRepository $vRequestRepository, RoleRepository $roleRepository, UserRepository $userRepository, StatusRepository $statusRepository, MailerInterface $mailer)
    {
        $this->vRequestRepository = $vRequestRepository;
        $this->userRepository = $userRepository;
        $this->statusRepository = $statusRepository;
        $this->roleRepository = $roleRepository;
        $this->mailer = $mailer;
    }

    #[Route('/admin/requests', name: 'get_requests', methods: 'GET')]
    public function index(Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $o = $request->query->get('o');
        $uid = $request->query->get('uid');
        $sid = $request->query->get('sid');

        $stats = $this->statusRepository->findAll();
        $status = [];
        foreach ($stats as $stat) {
            $status[] = [
                'id' => $stat->getId(),
                'name' => $stat->getName()
            ];
        }

        $vRequests = $this->vRequestRepository->findAll();

        if ($o) {
            $vRequests = $this->vRequestRepository->orderByCreated($o);
        } elseif ($uid) {
            $vRequests = $this->vRequestRepository->filterByUser($uid);
        } elseif ($sid) {
            $vRequests = $this->vRequestRepository->filterByStatus($sid);
        }

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

        return $this->render('admin/index.html.twig', [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'data' => $data,
            'status' => $status,
            'o' => $o,
            'uid' => $uid,
            'sid' => $sid,
        ]);
    }

    #[Route('/admin/respond/{id}', name: 'respond')]
    public function respond(Request $request, int $id): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vRequest = $this->vRequestRepository->findOneBy(['id' => $id]);

        $data = [
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

        if (!$vRequest) {
            return throw $this->createNotFoundException('No request made yet');
        }

        $form = $this->createForm(VResponseType::class, $vRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data_form = json_decode($request->getContent(), true);

            $reason = $form->get('reason')->getData();
            $status = $form->get('status')->getData();

            if ($reason) {
                $vRequest->setReason($reason);
            }
            if ($status) {
                $vRequest->setStatus($status);
            }

            empty($data_form['reason']) ? true : $vRequest->setReason($data_form['reason']);
            empty($data_form['status']) ? true : $vRequest->setStatus($data_form['status']);

            $role = $this->roleRepository->findOneBy(['id' => 2]);
            $userRole = $vRequest->getUser();
            if ($status->getId() == 2) {
                $role = $this->roleRepository->findOneBy(['id' => 3]);
                $userRole = $vRequest->getUser()->addRole($role);
            }
            $userRole->addRole($role);

            $updatedRequest = $this->vRequestRepository->respond($vRequest, $userRole);

            $statusName = $vRequest->getStatus()->getName();
            $admin = $user->getFirstname() . ' ' . $user->getLastname();
            $admin_email = $user->getEmail();
            $user = $vRequest->getUser()->getFirstname() . ' ' . $vRequest->getUser()->getLastname();
            $user_email = $vRequest->getUser()->getEmail();

            $this->sendMail($reason, $statusName, $admin, $admin_email, $user, $user_email);

            // $this->json($updatedRequest, Response::HTTP_OK);
            return $this->redirectToRoute('get_requests');
        }

        return $this->renderForm('admin/respond.html.twig', [
            'form' => $form,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'vr_firstname' => $data['firstname'],
            'vr_lastname' => $data['lastname'],
            'role' => $data['role'],
            'email' => $data['email'],
            'idImage' => $data['idImage'],
            'message' => $data['message'],
            'status' => $data['status'],
            'reason' => $data['reason'],
            'createdAt' => $data['createdAt'],
            'modifiedAt' => $data['modifiedAt'],
            'data' => $data,
        ]);
    }

    #[Route('/admin/revoke/{id}', name: 'revoke')]
    public function revoke(Request $request, int $id): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vRequest = $this->vRequestRepository->findOneBy(['id' => $id]);
        $userDetail = $this->userRepository->findOneBy(['id' => $vRequest->getUser()->getId()]);

        $data = [
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

        if (!$vRequest) {
            return throw $this->createNotFoundException('No request made yet');
        }

        $form = $this->createForm(VResponseType::class, $vRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data_form = json_decode($request->getContent(), true);

            $reason = $form->get('reason')->getData();
            $status = $form->get('status')->getData();

            if ($reason) {
                $vRequest->setReason("we apologize, requirement check not met");
            }
            if ($status) {
                $vRequest->setStatus($status);
            }

            empty($data_form['reason']) ? true : $vRequest->setReason($data_form['reason']);
            empty($data_form['status']) ? true : $vRequest->setStatus($data_form['status']);

            $role = $this->roleRepository->findOneBy(['id' => 3]);
            $userRole = $userDetail->removeRole($role);

            $updatedRequest = $this->vRequestRepository->revoke($vRequest, $userRole);

            $statusName = $vRequest->getStatus()->getName();
            $admin = $user->getFirstname() . ' ' . $user->getLastname();
            $admin_email = $user->getEmail();
            $user = $vRequest->getUser()->getFirstname() . ' ' . $vRequest->getUser()->getLastname();
            $user_email = $vRequest->getUser()->getEmail();

            $this->sendMail($reason, $statusName, $admin, $admin_email, $user, $user_email);

            // $this->json($updatedRequest, Response::HTTP_OK);
            return $this->redirectToRoute('get_requests');
        }

        return $this->renderForm('admin/respond.html.twig', [
            'form' => $form,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'vr_firstname' => $data['firstname'],
            'vr_lastname' => $data['lastname'],
            'role' => $data['role'],
            'email' => $data['email'],
            'idImage' => $data['idImage'],
            'message' => $data['message'],
            'status' => $data['status'],
            'reason' => $data['reason'],
            'createdAt' => $data['createdAt'],
            'modifiedAt' => $data['modifiedAt'],
            'data' => $data,
        ]);
    }

    public function sendMail(?string $response, string $status, string $admin, string $admin_email, string $user, string $user_email): Response
    {
        $response = (!empty($response)) ? $response : 'No reason specified';
        $email = (new TemplatedEmail())
            // ->getHeaders()
            //     ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply')
            ->from(Address::create($admin_email, $admin,))
            ->to(Address::create($user_email, $user))
            ->subject('Verification Request')
            // ->text("Request $status, Reason: $response")
            ->htmlTemplate('v_request/email.html.twig')
            ->context([
                'status' => $status,
                'reason' => $response,
                'user' => $user
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
        }

        return new Response('Email sent');
    }
}
