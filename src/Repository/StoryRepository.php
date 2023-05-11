<?php

namespace App\Repository;

use App\DTO\FilterStory;
use App\Entity\Story;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Story>
 *
 * @method Story|null find($id, $lockMode = null, $lockVersion = null)
 * @method Story|null findOneBy(array $criteria, array $orderBy = null)
 * @method Story[]    findAll()
 * @method Story[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private LoggerInterface $logger)
    {
        parent::__construct($registry, Story::class);
    }

    public function save(Story $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Story $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filterAll(FilterStory $dtoFilter){
        $this->logger->info("Filtermethode für Story wurde aufgerufen");
        $qb = $this->createQueryBuilder("b");
        if(!($dtoFilter->likes == 0 && $dtoFilter->dislikes == 0)){
            $this->logger->debug("Filter Like: {like}", ["like" => $dtoFilter->likes]);
            $this->logger->debug("Filter Dislike: {dislike}", ["dislike" => $dtoFilter->dislikes]);
            if(!($dtoFilter->likes == 0)){
                $qb = $qb->andWhere("b.likes >= :likes")
                    ->setParameter("likes", $dtoFilter->likes);
            }
            if(!($dtoFilter->dislikes == 0)){
                $qb = $qb->andWhere("b.dislikes >= :dislikes")
                    ->setParameter("dislikes", $dtoFilter->dislikes);
            }
        }
        if($dtoFilter->author){
            $qb = $qb->andWhere("b.author like :author")
                ->setParameter("author", $dtoFilter->author."%");
        }
        /*
        wir haben bei FilterStory.php noch weitere variabeln hinzugefügt, sodass wir sie hier benutzen können.
        Diese kann man dann einfach mitgeben beim Request Body
        */
        if($dtoFilter?->orderby){
            $this->logger->debug("OrderBy: {orderby}", ["orderby" => $dtoFilter->orderby]);
            $qb->orderBy($dtoFilter->orderby,$dtoFilter->orderdirection ?? "ASC");
        }
            return $qb
                ->getQuery()
                ->getResult();
    }

//    /**
//     * @return Story[] Returns an array of Story objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Story
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
