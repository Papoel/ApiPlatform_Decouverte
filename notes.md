# TODO FORMATION THOMAS BOILEAU

[//]: # (source:https://www.youtube.com/watch?v=GGftlmFoyWI)
add link to video here 

[Vidéo YouTube](https://www.youtube.com/watch?v=GGftlmFoyWI)

Timer: 40'

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



