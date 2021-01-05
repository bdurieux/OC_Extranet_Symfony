<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Account;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    
    /* @return Post[] Returns an array of Post objects
    */    
    public function findByIdActeur($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $sql = '
            SELECT id_post, p.id_user, id_acteur, date_add, post, prenom 
            FROM post p 
                LEFT JOIN account a ON a.id_user = p.id_user 
            WHERE p.id_acteur = :id_acteur 
            ORDER BY date_add DESC 
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(array('id_acteur' => $id));
        return $stmt->fetchAll();
        /* return $this->createQueryBuilder('p')
            ->select('p')
            ->from('post', 'p')
            ->leftJoin('account', 'a', 'WITH', 'a.id_user = p.id_user')
            ->where('p.id_acteur = ?1')
            ->orderBy('p.date_add','DESC')
            ->setParameter(1,$id)
            ->getQuery()
            ->getResult()
        ; */
    }
   

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
