<?php

namespace BUILDY\PlatformBundle\Entity;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPublishedQueryBuilder()
     {
       return $this
         ->createQueryBuilder('a')
         ->where('a.published = :published')
         ->setParameter('published', true)
       ;
     }

    public function getAdverts($page, $nbPerPage)
    {
      $query = $this->createQueryBuilder('a')
        // Jointure sur l'attribut image
        ->leftJoin('a.image', 'i')
        ->addSelect('i')
        // Jointure sur l'attribut categories
        ->leftJoin('a.categories', 'c')
        ->addSelect('c')
        ->orderBy('a.date', 'DESC')
        ->getQuery()
      ;

     $query
        // On définit l'annonce à partir de laquelle commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre d'annonce à afficher sur une page
        ->setMaxResults($nbPerPage)
      ;

      // Enfin, on retourne l'objet Paginator correspondant à la requête construite
      return new Paginator($query, true);
    }


    /**
     * conditions : postées durant l'année en cours
     * @param  QueryBuilder $qb [description]
     * @return [type]           [description]
     */
    public function whereCurrentYear(QueryBuilder $qb)
    {
      $qb
        ->andWhere('a.date BETWEEN :start AND :end')
        ->setParameter('start', new \Datetime(date('Y').'-01-01'))  // Date entre le 1er janvier de cette année
        ->setParameter('end',   new \Datetime(date('Y').'-12-31'))  // Et le 31 décembre de cette année
      ;
    }

    /**
     * [getAdvertWithApplications description]
     * @return [type] [description]
     */
    public function getAdvertWithApplications()
    {
      $qb = $this
        ->createQueryBuilder('a')
        ->leftJoin('a.applications', 'app')
        ->addSelect('app')
      ;
      return $qb
        ->getQuery()
        ->getResult()
      ;
    }

    /**
     * [getAdvertWithCategories description]
     * @param  array  $categoryNames [description]
     * @return [type]                [description]
     */
    public function getAdvertWithCategories(array $categoryNames)
    {
      $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.categories', 'cat')
            ->addSelect('cat')
            ->where($qb->expr()->in('c.name', $categoryNames))
      ;

      return $qb
        ->getQuery()
        ->getResult()
      ;
    }
}
