# Backend Projet SIS

Une brève description de ce que fait votre projet.

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)

## Installation

1. Clonez le dépôt :

    ```bash
    git clone https://github.com/votre-utilisateur/votre-repo.git
    cd votre-repo
    ```

2. Démarrez les conteneurs Docker :

    ```bash
    docker-compose up -d
    ```

3. Installez les dépendances avec Composer :

    ```bash
    docker-compose exec php composer install
    ```

4. Créez le fichier `.env` à partir de `.env.example` et ajustez les variables d'environnement si nécessaire :

    ```bash
    cp .env.example .env
    ```

5. Initialisez la base de données :

    ```bash
    docker-compose exec php bin/console doctrine:database:create
    docker-compose exec php bin/console doctrine:migrations:migrate
    ```

6. (Optionnel) Chargez les fixtures :

    ```bash
    docker-compose exec php bin/console doctrine:fixtures:load
    ```

## Utilisation

Pour accéder à l'application, ouvrez votre navigateur et accédez à `http://localhost`.

### Commandes utiles

- Accéder au conteneur PHP :

    ```bash
    docker-compose exec php bash
    ```

- Exécuter des tests :

    ```bash
    docker-compose exec php bin/phpunit
    ```

- Lancer les migrations :

    ```bash
    docker-compose exec php bin/console doctrine:migrations:migrate ▋
