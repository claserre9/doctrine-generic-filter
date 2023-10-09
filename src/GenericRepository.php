<?php

namespace App;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

class GenericRepository
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_LIMIT = 10;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getPaginatedResults(string $entityClass, int $page = self::DEFAULT_PAGE, int $limit = self::DEFAULT_LIMIT, ?array $filters = [], ?array $orderBy = []): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $page = $filters['page'] ?? $page;
        $limit = $filters['limit'] ?? $limit;
        unset($filters['page'], $filters['limit']);
        $alias = 'x';
        $this->applyFilters($queryBuilder, $alias, $entityClass, $filters);
        // Add ORDER BY clause if orderBy is provided
        $this->applyOrderBy($orderBy, $queryBuilder, $alias);
        $query = $queryBuilder->getQuery()->setFirstResult($limit * ($page - 1))->setMaxResults($limit)
        ;
        $paginator = new Paginator($query);
        $total = count($paginator);
        $totalPages = intval(ceil($total / $limit));
        $results = $paginator->getQuery()->getResult();
        return $this->paginate($results, $total, $limit, $page, $totalPages);
    }

    public function getResults(string $entityClass, ?array $filters = [], ?array $orderBy = []): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $alias = 'y';
        $this->applyFilters($queryBuilder, $alias, $entityClass, $filters);
        // Add ORDER BY clause if orderBy is provided
        $this->applyOrderBy($orderBy, $queryBuilder, $alias);
        $results = $queryBuilder->getQuery()->getResult();
        return ["data" => $results];
    }

    private function applyFilters(QueryBuilder $queryBuilder, string $alias, string $entityClass, array $filters): void
    {
        $queryBuilder->select($alias)->from($entityClass, $alias)
        ;
        $parameterIndex = 0;
        foreach ($filters as $queryParam => $expression) {
            $field = "$alias.$queryParam";
            foreach ($expression as $operator => $value) {
                $paramName = 'value' . $parameterIndex++;
                if (in_array(strtolower($operator), ['in', 'notin'])) {
                    if (!is_array($value)) {
                        $value = str_replace(' ', '', $value);
                        $value = array_values(explode(',', $value));
                    }
                } elseif (in_array(strtolower($operator), ['like', 'notlike'])) {
                    $value = "%{$value}%";
                } elseif ($operator === 'between') {
                    $startValue = $value['start'];
                    $endValue = $value['end'];
                    $startParamName = 'value' . $parameterIndex++;
                    $endParamName = 'value' . $parameterIndex++;
                    $queryBuilder->andWhere($queryBuilder->expr()->between($field, ":$startParamName", ":$endParamName"))->setParameter($startParamName, $startValue)->setParameter($endParamName, $endValue)
                    ;
                    continue;
                }
                $queryBuilder->andWhere($queryBuilder->expr()->$operator($field, ":$paramName"))->setParameter($paramName, $value)
                ;
            }
        }
    }

    /**
     * @param mixed $results
     * @param int $total
     * @param mixed $limit
     * @param mixed $page
     * @param int $totalPages
     * @return array
     */
    private function paginate(mixed $results, int $total, mixed $limit, mixed $page, int $totalPages): array
    {
        return [
            "data" => $results,
            "meta" => [
                "total" => $total,
                "perPage" => $limit,
                "currentPage" => $page,
                "lastPage" => $totalPages,
                "from" => min(($page - 1) * $limit + 1, $total),
                "to" => min($page * $limit, $total),
                ],
            ];
    }

    /**
     * @param array|null $orderBy
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string $alias
     * @return void
     */
    private function applyOrderBy(?array $orderBy, QueryBuilder $queryBuilder, string $alias): void
    {
        if (is_array($orderBy)) {
            foreach ($orderBy as $column => $direction) {
                $queryBuilder->addOrderBy("$alias.$column", $direction);
            }
        }
    }
}
