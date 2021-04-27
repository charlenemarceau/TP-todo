# Projet ToDoList
- Installation minimale du package Symfony
- Voir les composants requis au fur et à mesure
- Gestion de données avec système CRUD
- Class Formulaires et contrôles (contraintes)
- 2 entités relation ManyToOne
- Deploiement sur Heroku

# Etape 1
Check système
```bash
symfony check:requirements
```

## Install de composants
Voir ces adresses :
- https://packagist.org/
- https://flex.symfony.com/
- Voir doc guide : https://symfony.com/doc/current/doctrine.html

## Configuration de la base de données : 
nom = db_dev_todolist
On a besoin de doctrine : ORM
```bash
# on tape symfony console doctrine et on nous donne :
composer require symfony/orm-pack
# Le fichier .env a été modifié
DATABASE_URL="mysql://root:@127.0.0.1:3306/db_dev_todolist"
# puis
symfony console doctrine:database:create
```
#
# Entités
# Principe de relation
- Une todo appartient à une catégorie
- Une catégorie contient 0 ou plusieurs todo.
## Entités
Category(name(string))
Todo (title(string), content(text), created_at(datetime), updated_at(datetime), date_for(datetime))
```bash
symfony console make:entity Category
# puis 
symfony console make:entity Todo
#puis 
symfony console make:entity Todo
# on ajoute le champ category
# on choisit comme type : relation
```

#
## Migration
```bash
symfony console make:migration
symfony console doctrine:migrations:migrate
```

#
## Fixtures
```bash
composer require orm-fixtures --dev
```

## Alimenter les tables
__NB__ : 
- voir comment définir des dates de création et d'update dès la création d'une Todo.
- constructeur de la class Todo

### Analyse

1. La table Category doit être remplie en premier
- On part d'un tableau de catégories.
 - Pour chaque catégorie je veux l'enregistré dans la table physique.
        Sous Symfony tout passe par l'objet --> class Category
2. La table Todo
- on crée un objet Todo.
- __NB__ la méthode `setCategory` qiu a besoin d'un objet Category comme argument.

#
# Controllers
## TestController
L'objectif est de voir le format de rendu que propose le controller, sachant que Twig n'est pas installé.
```bash
symfony console make:controller Test
```
## Installer Twig
```bash
composer require Twig
```
## TodoController
```
symfony console make:controller Todo
# on a une vue crée dans le dossier Template
```
Le controller va récupérer notre premier enregistrement de la table Todo et le passer à la vue `todo/index`

La mise en forme est gérée par des tables Bootstrap
### La page d'accueil (voir)
1. une méthode et sa route
```php 
    # Le repository en injection de dépendance
    public function detail($id, TodoRepository $repo): Response 
```
2. une vue dans template Todo
3. Le lien qui dans la page d'accueil

#
# Formulaires
## Install
```bash
composer require form validator
```
## Generate form
## Etape 1 
Génération de la classe du nom que vous voulez
```bash
symfony console make:form
#TodoFormType choisit
```
## Etape 2
On crée une méthode dans le TodoController, c'est la méthode `create()`
On va créer le lien du bouton pour tester le cheminement jusqu'à la vue `create.html.twig`
### Problèmatique des routes
```bash
## Besoin d'installer le profiler pour débugger
composer require --dev symfony/profiler-pack
## aussi
symfony console debug:router
```
#### Voir :
1. la forme de urls. exp : `/todo, /todo/1, todo/1/edit`
2. L'ordre de placement des méthodes peut influer
3. La posssibilité d'ajouter un paramètre de priorité (à voir)

## Etape 3
Gestion du formulaire dans la méthode adéquate du controller.
Affichage du formulaire dans la vue 
### Amélioration du visuel
Dans config/package/twig.yaml
```yaml
    form_themes: ['bootstrap_4_layout.html.twig']
```

### Problèmatique du champ category
Il fait référence à une relation avec une entité.
On va ajouter des types à la classe `TodoFormType`
### Ajouter d'autres types
Voir la doc. Plusieurs options concurrentes
## TodoController : edit()
- on installe un bundle dont le rôle est de faire la correspondance entre une url avec l'id d'un objet et l'objet passé en paramètre.
```bash
composer req sensio/framework-extra-bundle
``` 

## Crée un message Flash
- voir la doc. taper : `Flash` et sélectionner `Flash Message`
- 1 partie dans le controller : la construction du message
- 1 autre dans la vue : l'affichage selon le choix pris dans la doc
## TodoController : delete
### Méthode 1 
- Un lien depuis la page d'accueil 
- Ici le lien
## Méthode 2
- lien dans la page update
- on ajoute une confirmation en javascript
- __NB__ : Attention à l'emplacement de `{% block javascripts %}{% endblock %}`
#
# Ahouter une navbar
- un fichier _navbar.html.twig, crée avec une navbar Bootstrap
    - un titre
    - un bouton accueil
    - un menu déroulant
- inclure dans base.html.twig dans un {% block navbar%}
