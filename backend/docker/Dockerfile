FROM php:8.4-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USERNAME=appuser

# ROOT (System config) STAGE
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

RUN groupadd -g $GROUP_ID $USERNAME && \
    useradd -u $USER_ID -g $USERNAME -m $USERNAME

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R $USERNAME:$USERNAME /var/www/html


# NON-Root (application) stage
USER $USERNAME

RUN composer install --no-interaction --no-dev --optimize-autoloader

ENTRYPOINT ["bash", "docker/docker-entrypoint.sh"]

EXPOSE 8000
