<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use AppBundle\Entity\Transaction;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * TransactionRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionRepository extends EntityRepository
{
    /**
     * @param Transaction|null $transaction
     *
     * @return array
     */
    public function getFiltredTransaction($transaction = null)
    {
        if (!$transaction instanceof Transaction) {
            return $this->findAll();
        }
        $queryBuilder = $this->createQueryBuilder('t')
            ->select()
            ->leftJoin('t.tags', 'tag');

        $filter = [];
        if ($transaction->getTitle()) {
            $filter['t.title'] = [
                'operator' => 'LIKE',
                'values' => '%'.$transaction->getTitle().'%',
            ];
        }
        if ($transaction->getAmount()) {
            $filter['t.amount'] = [
                'operator' => '=',
                'values' => $transaction->getAmount(),
            ];
        }
        if ($transaction->getDescription()) {
            $filter['t.description'] = [
                'operator' => 'LIKE',
                'values' => '%'.$transaction->getDescription().'%',
            ];
        }
        if (null !== $transaction->getIsValid()) {
            $filter['t.isValid'] = [
                'operator' => '=',
                'values' => $transaction->getIsValid(),
            ];
        }
        if (null !== $transaction->getIsInput()) {
            $filter['t.isInput'] = [
                'operator' => '=',
                'values' => $transaction->getIsInput(),
            ];
        }

        if ($transaction->getCategory()) {
            $filter['t.category'] = [
                'operator' => '=',
                'values' => $transaction->getCategory()->getId(),
            ];
        }
        if (!$transaction->getTags()->isEmpty()) {
            $collection = $transaction->getTags();
            $filter['tag.id'] = [
                'operator' => 'IN',
                'values' => $collection->map(function (Tag $entity) {
                    return $entity->getId();
                })->toArray(),
            ];
        }

        $this->setConditionFilters($queryBuilder, $filter);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $month
     *
     * @return array
     */
    public function getFiltredTransactionByMonth($month)
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.isValid = 1')
            ->andWhere('MONTH(t.createdAt) = :month')
            ->andWhere('YEAR(t.createdAt) = :year')
            ->setParameter(':month', $month)
            ->setParameter(':year', date('Y'));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $query
     * @param array        $filters
     */
    private function setConditionFilters(QueryBuilder $query, array $filters = [])
    {
        $loop = 0;

        foreach ($filters as $key => $value) {
            $operator = $value['operator'];
            $whereCondition = $key.' '.$operator.' :param'.$loop;

            if ('IN' === $operator) {
                $whereCondition = $key.' IN (:param'.$loop.')';
            }
            if (0 == $loop) {
                $query->where($whereCondition);
            } else {
                $query->andwhere($whereCondition);
            }
            $query->setParameter(':param'.$loop, $value['values']);
            ++$loop;
        }
    }

    public function getTreasuryInfos($month)
    {
        $inputInfos = $this->getOperationData($month, true);
        $outputInfos = $this->getOperationData($month, false);
        $noInfos = 'no infos';

        return [
            'countInputs' => isset($inputInfos['countInput']) ? $inputInfos['countInput'] : $noInfos,
            'countOutputs' => isset($outputInfos['countOutput']) ? $outputInfos['countOutput'] : $noInfos,
            'treasury' => (isset($inputInfos['countInput']) && isset($outputInfos['countOutput'])) ? $inputInfos['countInput'] - $outputInfos['countOutput'] : $noInfos,
        ];
    }

    private function getOperationData($month, $isInput)
    {
        $operationLabel = 'Input';
        if (!$isInput) {
            $operationLabel = 'Output';
        }
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('count(t) as count'.$operationLabel.' ,SUM(t.amount) as sum'.$operationLabel)
            ->where('t.isValid = 1')
            ->andWhere('MONTH(t.createdAt) = :month')
            ->andWhere('YEAR(t.createdAt) = :year')
            ->andWhere('t.isInput = :isInput')
            ->setParameter(':month', $month)
            ->setParameter(':isInput', $isInput)
            ->setParameter(':year', date('Y'));

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
