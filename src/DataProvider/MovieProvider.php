<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Movie;
use App\Repository\MovieRepository;

/**
 * Class MovieProvider
 * Return a collection of movies.
 * @package App\DataProvider
 * @author  Thomas Boileau
 * @link https://www.youtube.com/watch?v=GGftlmFoyWI&ab_channel=ThomasBoileau
 * use-case:
 * - set by default the page number to 1
 */
final class MovieProvider implements RestrictedDataProviderInterface, ContextAwareCollectionDataProviderInterface
{
    public function __construct(private MovieRepository $movieRepository){}

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Movie::class === $resourceClass && 'get' === $operationName;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        // var_export($context);
        $page = isset($context['filters']) ? ($context['filters']['page'] ?? 1) : 1;

        return $this->movieRepository->getMovies((int) $page);
    }

    public function getItem(string $resourceClass, string $operationName = null, array $context = []): Movie
    {
        return new Movie();
    }
}

