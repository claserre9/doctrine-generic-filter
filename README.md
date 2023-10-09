# Doctrine Generic Filter

This package provides a `GenericRepository` class which is a versatile utility class designed 
to simplify database operations using the Doctrine ORM library. 
It provides methods for retrieving paginated results, applying filters, 
and sorting data from your database entities. This README file will guide you through using this class effectively.


## Table of contents

- Installation
- Usage
- Methods
- Examples
- Contributing
- Licence

## Installation
To use the `GenericRepository` class, you need to have the Doctrine ORM 
library set up in your PHP project. 

``` bash
composer req claserre/doctrine-generic-filter
```

## Usage
The `GenericRepository` class provides methods for retrieving data from your database entities with various options:

- `getPaginatedResults`: Retrieves paginated results with optional filters and sorting.
- `getResults`: Retrieves results without pagination but with optional filters and sorting.

Create an instance of the GenericRepository class by injecting the EntityManagerInterface:

```php
$entityManager = ...; // Instantiate your EntityManager
$repository = new GenericRepository($entityManager);
```

## Methods
### `getPaginatedResults`

```php
public function getPaginatedResults(
    string $entityClass,
    int $page = self::DEFAULT_PAGE,
    int $limit = self::DEFAULT_LIMIT,
    ?array $filters = [],
    ?array $orderBy = []
): array
```

- `$entityClass` (string): The name of the entity for which you want to retrieve results.
- `$page` (int): The current page number (default is 1).
- `$limit` (int): The number of results to fetch per page (default is 10).
- `$filters` (array|null): An array of filters to apply to the query.
- `$orderBy` (array|null): An array specifying the sorting order for the results.

Returns an array containing the paginated results along with pagination metadata.

### `getResults`

```php
public function getResults(
    string $entityClass,
    ?array $filters = [],
    ?array $orderBy = []
): array
```

`$entityClass` (string): The name of the entity for which you want to retrieve results.
`$filters` (array|null): An array of filters to apply to the query.
`$orderBy` (array|null): An array specifying the sorting order for the results.

Returns an array containing the results without pagination.

## Examples

Here are some examples of how to use the `GenericRepository` class:

```php
// Create an instance of GenericRepository (assuming $entityManager is already instantiated).
$repository = new GenericRepository($entityManager);

// Example 1: Get paginated results with filters and ordering.
$results = $repository->getPaginatedResults('Your\Entity\Class', 2, 15, ['column' => ['operator' => 'value']], ['column' => 'ASC']);

// Example 2: Get results without pagination with filters and ordering.
$results = $repository->getResults('Your\Entity\Class', ['column' => ['operator' => 'value']], ['column' => 'ASC']);
```

This is the list of all operators supported

- `eq` (equal)
- `neq` (not equal)
- `gt` (greater than)
- `lt` (less than)
- `gte` (greater than or equal)
- `lte` (less than or equal)
- `in`
- `notin` (not in)
- `between`

### Some concrete examples
```php
/** Retrieve paginated results for the User entity with a filter
* on the gender field where it should be equal to 'Male'
*/

$filters = ['gender' => ["eq" => 'Male'],];
$results = $genericRepository->getPaginatedResults(User::class, 1, 10, $filters);
```

```php
/** Retrieve paginated results for the User entity with a filter 
 * on the age field where it should be less than or equal to 20*
 */
$filters = ['age' => ["lte" => 20]];
$results = $genericRepository->getPaginatedResults(User::class, 1, 20, $filters);
```

```php
/**
* Retrieve paginated results for the User entity with a filter 
 * on the age field where the age should be between 20 and 30
 */
$filters = ['age' => ["between" => ["start" => 20, "end" => 30]]];
$results = $genericRepository->getPaginatedResults(User::class, 1, 10, $filters);
```

```php
/**
* Multiple filters and order by
 */
$orderBy = [
    'age' => 'DESC',
    'id' => 'DESC'
];

$filters = [
    'country' => ['eq' => 'China'],
    'gender' => ['eq' => 'Male'],
];

$results = $genericRepository->getPaginatedResults(User::class, 1, 10, $filters, $orderBy);
```

## Contributing
Feel free to contribute to this project by opening issues or pull requests on the GitHub repository.

## Licence
This project is licensed under the MIT License.


This README file provides an overview of the `GenericRepository` class, its usage, and how to get started with it in your PHP project. If you have any questions or encounter issues, please refer to the provided examples and documentation in the code or reach out for support.

Happy coding!