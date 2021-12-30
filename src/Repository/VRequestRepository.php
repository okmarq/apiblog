<?php

namespace App\Repository;

use App\Entity\Role;
use App\Entity\Status;
use App\Entity\User;
use App\Entity\VRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method VRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method VRequest[]    findAll()
 * @method VRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VRequestRepository extends ServiceEntityRepository
{
    private $manager;
    private $registry;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, VRequest::class);
        $this->manager = $manager;
        $this->registry = $registry;
    }

    public function create(string $idImage, string $message, int $id)
    {
        $user = $this->registry->getRepository(User::class)->find($id);

        $status = $this->registry->getRepository(Status::class)->find(1);

        $newRequest = new VRequest();

        $newRequest
            ->setUser($user)
            ->setIdImage($idImage)
            ->setMessage($message)
            ->setStatus($status)
            ->setCreatedAt();

        $this->manager->persist($newRequest);
        $this->manager->flush();
    }

    public function respond(string $reason, int $status_id, int $id): VRequest
    {
        $status = $this->registry->getRepository(Status::class)->find($status_id);
        $newRequest = new VRequest();
        $newRequest
            ->setReason($reason)
            ->setStatus($status);

        // update user role to ROLE_BLOGGER
        $updateUser = new User();
        if ($status_id === 2) {
            $role = $this->registry->getRepository(Role::class)->find(3);

            $updateUser
                ->addRole($role);
        }

        // send mail to $data['email']
        $updateUser->getEmail();




        $this->manager->persist($newRequest);
        $this->manager->persist($updateUser);
        $this->manager->flush();

        return $newRequest;
    }

    public function update(VRequest $vRequest): VRequest
    {
        $this->manager->persist($vRequest);
        $this->manager->flush();

        return $vRequest;
    }

    public function delete(VRequest $vRequest)
    {
        $this->manager->persist($vRequest);
        $this->manager->flush();
    }

    // /**
    //  * @return VRequest[] Returns an array of VRequest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VRequest
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
