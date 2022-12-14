<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\GiftInQuestionnaire;
use App\Entity\Questionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GiftInQuestionnaire>
 *
 * @method GiftInQuestionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method GiftInQuestionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method GiftInQuestionnaire[]    findAll()
 * @method GiftInQuestionnaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GiftInQuestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GiftInQuestionnaire::class);
    }
//
//    public function getAllGiftsInQuestionnaire(Questionnaire $questionnaire){
//        $qb = $this->createQueryBuilder('g');
//        $qb->where('g.questionnaireId = :questionnaire')
//            ->setParameter('questionnaire', $questionnaire);
//
//        $query = $qb->getQuery();
//        $result = $query->getResult();
//        return $result;
//    }

    public function save(GiftInQuestionnaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GiftInQuestionnaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrderedByVotesAmount(Questionnaire $questionnaire){
        return $this->createQueryBuilder('q')
            ->andWhere('q.questionnaireId = :val')
            ->setParameter('val', $questionnaire)
            ->orderBy('q.voteAmount', 'DESC')
            ->setMaxResults($questionnaire->getEndGiftAmount())
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return GiftInQuestionnaire[] Returns an array of GiftInQuestionnaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GiftInQuestionnaire
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
