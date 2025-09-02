FROM php:8.4-fpm

# User config
ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USERNAME=appuser

RUN groupadd -g $GROUP_ID $USERNAME && \
    useradd -u $USER_ID -g $USERNAME -m $USERNAME


# Dependencies install
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-scripts


# Owner to appuser
RUN chown -R $USERNAME:$USERNAME /var/www/html

USER $USERNAME

ENTRYPOINT ["bash", "docker/docker-entrypoint.sh"]

EXPOSE 8000
