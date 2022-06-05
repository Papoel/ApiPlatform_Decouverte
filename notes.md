<style>
    .folder {
        color: #c159d5;
        font-style: italic;
        font-family: sans-serif;
        font-size: 14px;
        letter-spacing: 1px;
    }
    .folder::before {
        content: "📁";
    }
</style>

<span class="folder">/src/etc ...</span>
# TODO FORMATION THOMAS BOILEAU

[//]: # (source:https://www.youtube.com/watch?v=GGftlmFoyWI)
add link to video here 

[Vidéo YouTube](https://www.youtube.com/watch?v=GGftlmFoyWI)

Timer: 1H20'

# DataProvider

### Qu'est-ce que c'est ?

Le DataProvider est un service qui permet de fournir des données.

### Méthodes

## **Supports**

Sert à savoir si on doit utilise ce Provider grace à la 
_$ressourceClass_

## **GetCollection**

À implémenter avec le Repository, en se servant de ma méthode
custom _**getMovies**_, cette méthode à besoin d'une page.

Pour récupérer cette page qu'on passe ou pas dans la query
nous allons nous aider de notre $context.

Je récupère ces infos en faisant :
`$context['filters']['page']`.

Je vérifie si ces infos sont données et si ce n'est pas le
cas je définis la valeur par défaut à 1.

Lorsque je reçois mes données au format `json` je
constate que le genre de mes films n'est pas renvoyé
directement.
L'API me renvoie un **`iri`**, il s'agit en fait de 
l'`url` pour récupérer la donnée.

Au final cela pointe sur la route /genre/{id}` de l'API.

Pour exploiter cette donnée je dois utiliser
**`API Property`**
je dois copier le groupe créer dans Movie ici `#[ORM\Groups(['collection'])]`
et le coller dans Genre


---

#### CollectionDataProvider

Récupère les données d'une collection

#### ItemDataProvider

Récupère les données d'un élément.

## DataPersister

### Qu'est-ce que c'est ?

Le DataPersister est un service qui permet de faire une action sur
une donnée ou une collection.

---

# SubRessource

Permet de créer une sous ressource, en créant de nouvelles routes
pour récupérer les donnés d'une ressource.
Dans l'exemple ci-dessous, je crée une sous ressource pour récupérer
les acteurs et réalisateurs d'un film.

---

# La Validation

# Data Transformer Object

## Qu'est-ce que c'est ?

Un DTO peut être soit un `input` soit un `output`.

**`Un Input`** est la donnée qui va être envoyée à l'API via la requête.

**`L'Output`** est la donnée qui va être envoyée au client (La Response) en résultat de la requête.

**`Controller`**

**`Data Persister`** Est le service que l'on va implémenter qui va permettre 
de persister une donnée dans la base de donnée.

---

# Query Custom

## On va récupérer aléatoirement un film.

On va l'appeler `random` dans `collection_operation`car on ne vas pas lui passer d'id

Etape 1 : On va lui passer un chemin ( ce sera `/api/movies/random` ).

En faisant cela lorsque je me rends sur mon URL j'obtiens cette
erreur :

```php
The route "random" of the resource "Movie" was not found.
```

Pour corriger cette erreur, je dois créer un Controller.
Je me rends dans le dossier `src/Controller` et je crée un nouveau
Controller qui va se nommer `RandomMovie`.

ce Controller sera une `final class`.

J'écris ce code dans le fichier `RandomMovie.php` et je fais ceci:

<span class="folder">/src/Controller/RandomMovie.php</span>

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class RandomMovie
{
    public function __invoke(): void
    {
        // TODO: Implement __invoke() method.
    }
}
```

Je me rends dans mon fichier `services.yaml` et j'ajoute 
la partie `App\Controller\`:

<span class="folder">config/services.yaml<span>

```yaml
    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'
    App\Controller\:
        resource: '../src/Controller/'
        tags:
            - controller.service_arguments
```

Une fois ceci fait je vais maintenant créer une fonction
`getRandomMovie`dans mon `MovieRepository`.

<span class="folder">src/Repository/MovieRepository.php</span>

```php
public function getRandomMovie(): Movie
    {
        return $this->createQueryBuilder('m')
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }
```

Ici la méthode `RAND()` n'est pas reconnu je dois installer
le bundle `Berberlei` avec la commande
`composer require beberlei/doctrineextensions`. pour pouvoir l'utiliser.

Une fois le bundle installé je me rends dans le fichier
`config/packages/doctrine.yaml` et je rajoute les lignes suivantes :

> ︎⚠️ **Uniquement si la BDD est en MySQL**

<span class="folder">config/packages/doctrine.yaml</span>

```yaml
    orm:
      auto_generate_proxy_classes: true
      naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
      auto_mapping: true
      mappings:
        App:
          is_bundle: false
          dir: '%kernel.project_dir%/src/Entity'
          prefix: 'App\Entity'
          alias: App
        dql:
          numeric_functions:
            rand: DoctrineExtensions\Query\Mysql\Rand
        
```

> ✅ **En SqlLite**

<span class="folder">config/packages/doctrine.yaml</span>

```yaml
orm:
  auto_generate_proxy_classes: true
  naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
  auto_mapping: true
  mappings:
    App:
      is_bundle: false
      dir: '%kernel.project_dir%/src/Entity'
      prefix: 'App\Entity'
      alias: App
    dql:
    numeric_functions:
      rand: DoctrineExtensions\Query\Sqlite\Random
```

> 🔆 En gros pour `Sqlite` je dois modifier partout ou j'ai
`rand` en `random`.

<span class="folder">src/Repository/MovieRepository.php</span>

```php
public function getRandomMovie(): Movie
    {
        return $this->createQueryBuilder('m')
            ->orderBy('RANDOM()')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }
```

Maintenant je me rends dans mon Entity `Movie` et je modifie le code pour qu'il
prenne un compte le controller et non plus la route en dur.

<span class="folder">src/Entity/Movie.php</span>
```php
    collectionOperations: [
        'get' => [
            'normalization_context' =>  ['groups' => ['collection']],
        ],
        'post',
        'random' => [
            'controller' => RandomMovie::class,
            'path' => '/movies/random',
            'output' => Movie::class,
            'method' => Request::METHOD_GET,
            'pagination_enabled' => false,
            'normalization_context' =>  ['groups' => ['item']],
        ],
    ],
```

## Création d'un Décorateur.

Je commence par créer un nouveau dossier `OpenApi`dans `src`
dans lequel je vais créer un fichier `MovieApiFactory.php`

<span class="folder">src/OpenApi/MovieApiFactory.php</span>

```php
<?php

namespace App\openApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

final class MovieApifactory implements openApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $pathItem = $openApi->getPaths()->getPath('/api/movies/random');

        $operation = $pathItem->getGet();

        dd($operation);

        return $openApi;
    }
}
```

Je dois décorer mon `services.yaml`

<span class="folder">config/services.yaml</span>

```yaml
    App\Controller\:
        resource: '../src/Controller/'
        tags:
            - controller.service_arguments
    # add more service definitions when explicit configuration is needed
    # please note that last definitions always *replace* previous ones

    App\OpenApi\MovieApiFactory:
        decorates: 'api_platform.openapi.factory'
        arguments: [ '@App\OpenApi\MovieApiFactory.inner' ]
        autoconfigure: false

```

<span class="folder">src/OpenApi/MovieApiFactory.php</span>

```php
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

```

Cette classe me retournera un objet OpenApi qui retournera aléatoirement
un élément de la collection.
