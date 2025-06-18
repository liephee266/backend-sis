FROM php:8.2

# Installer les paquets système nécessaires
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    default-mysql-client

# Installer et configurer l'extension PDO MySQL (et nettoyer les sources)
RUN docker-php-source extract && \
    apt-get install -y libpq-dev && \
    docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd && \
    docker-php-ext-install pdo_mysql && \
    docker-php-source delete

# Charger la configuration PHP personnalisée
COPY ./configdocker/php.ini /usr/local/etc/php/

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le code source de l’application
COPY . /var/www/html

# Autoriser Composer à s’exécuter en tant que superutilisateur
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash

# Ajouter Symfony CLI au PATH en utilisant le chemin absolu /root
ENV PATH="/root/.symfony5/bin:${PATH}"

# Installer les dépendances PHP sans exécuter les scripts
RUN composer install --no-scripts --ignore-platform-reqs
#
RUN composer install --ignore-platform-reqs

# Ajuster les permissions pour l’utilisateur www-data
RUN chown -R www-data:www-data /var/www/

# Exposer le port utilisé par le serveur PHP intégré
EXPOSE 8000

# Démarrer le serveur PHP en exec form pour une propagation correcte des signaux
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
