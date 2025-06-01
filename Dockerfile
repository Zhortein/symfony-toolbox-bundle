# Utilise l'image officielle PHP 8.3
FROM php:8.3-cli

# Install dépendances système & extensions PHP utiles (voir composer.json + bundles)
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libicu-dev libxml2-dev libxslt-dev libpng-dev libjpeg-dev libfreetype6-dev libpq-dev libonig-dev \
    && docker-php-ext-install \
        intl \
        zip \
        xsl \
        gd \
        mbstring \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        calendar \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
    git make nano vim less bash-completion \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer v2 (la version officielle à jour)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /app

# Copier le code source
COPY . .

# Installer les dépendances du bundle (prod + dev)
RUN composer install --prefer-dist --no-interaction

# Entrypoint par défaut : shell
CMD [ "bash" ]
