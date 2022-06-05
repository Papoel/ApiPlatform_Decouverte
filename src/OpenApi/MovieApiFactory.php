<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

/**
 * Retrieve Random Movie Resource
 * From T.Boileau YouTube Live
 * Code Refacto by MySelf
 */
final class MovieApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private readonly OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this
            ->decorated
            ->__invoke($context)
        ;

        $randomItem = $openApi
            ->getPaths()
            ->getPath('/api/movies/random')
        ;

        $getItem = $openApi
            ->getPaths()
            ->getPath('/api/movies/{id}')
        ;

        if (isset($randomItem)) {
            $randomOperation = $randomItem
                ->getGet()
            ;
        }

        if (isset($getItem)) {
            $getOperation = $getItem
                ->getGet()
            ;
        }

        if (isset($randomOperation, $getOperation)) {
            $randomOperation
                ->addResponse(
                    $getOperation->getResponses()[200],
                    200
                );
        }

        if (isset($randomOperation)) {
            $randomOperation = $randomOperation
                // Summary => Définition principale
                ->withSummary('Retrieve Random Movie resource. ')
                // Description => Description détaillée de la fonction.
                ->withDescription('Récupère des ressources cinématographiques aléatoires');
        }

        if (isset($randomOperation)) {
            $randomItem = $randomItem
                ->withGet($randomOperation)
            ;
        }

        $openApi
            ->getPaths()
            ->addPath(
                '/api/movies/random',
                $randomItem
            )
        ;

        return $openApi;
    }
}
