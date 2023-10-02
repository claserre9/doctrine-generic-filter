<?php

namespace App;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;


class GenericRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieves paginated results for a given entity class.
     *
     * @param string $entityClass The class name of the entity.
     * @param int $page The page number to retrieve.
     * @param int $limit The maximum number of results per page.
     * @param array $queryParams Additional query parameters.
     * @throws Exception
     * @return array The paginated results.
     */
    public function getPaginatedResults(
        string $entityClass,
        int $page = 1,
        int $limit = 10,
        array $queryParams = []
    ): array {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $page = $queryParams['page'] ?? $page;
        $limit = $queryParams['limit'] ?? $limit;
        unset($queryParams['page'], $queryParams['limit']);

        $alias = 'x';
        $this->applyFilters($queryBuilder, $alias, $entityClass, $queryParams);
        $query = $queryBuilder->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        $paginator = new Paginator($query);
        $total = count($paginator);
        $totalPages = ceil($total / $limit);
        $results = $paginator->getQuery()->getResult();

        return [
            "total" => $paginator->count(),
            "currentPage" => $page,
            "totalPages" => $totalPages,
            "results" => $results
        ];
    }

    /**
     * Retrieves non-paginated results from the specified entity class based on the provided query parameters.
     *
     * @param string $entityClass The fully qualified class name of the entity.
     * @param array $queryParams An array of query parameters.
     * @throws Exception
     * @return array The non-paginated results.
     */
    public function getResults(
        string $entityClass,
        array $queryParams = []
    ): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $alias = 'y';
        $this->applyFilters($queryBuilder, $alias, $entityClass, $queryParams);
        return $queryBuilder->getQuery()->getResult();
    }
    /**
     * Apply filters to the query builder.
     *
     * @param QueryBuilder $queryBuilder The query builder object.
     * @param string $alias The alias for the entity class.
     * @param string $entityClass The fully qualified class name of the entity.
     * @param array $queryParams An array of query parameters.
     * @throws Exception
     * @return void
     */
    private function applyFilters(
        QueryBuilder $queryBuilder,
        string $alias,
        string $entityClass,
        array $queryParams): void
    {
        $queryBuilder
            ->select($alias)
            ->from($entityClass, $alias);

        $parameterIndex = 0;
        foreach ($queryParams as $queryParam => $expression) {
            $field = "$alias.$queryParam";

            foreach ($expression as $operator => $value) {
                if (in_array(strtolower($operator), ['in', 'notin'])) {
                    $value = explode(',', $value);
                } elseif (in_array(strtolower($operator), ['like', 'notlike'])) {
                    $value = "%{$value}%";
                } elseif ($operator === 'between') {
                    $startValue = $value['start'];
                    $endValue = $value['end'];
                    $startParamName = 'value' . $parameterIndex++;
                    $endParamName = 'value' . $parameterIndex++;
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->between($field, ":$startParamName", ":$endParamName"))
                        ->setParameter($startParamName, $startValue)
                        ->setParameter($endParamName, $endValue);
                    continue;
                }
                $paramName = 'value' . $parameterIndex++;
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->$operator($field, ":$paramName"))
                    ->setParameter($paramName, $value);
            }
        }
    }
}
