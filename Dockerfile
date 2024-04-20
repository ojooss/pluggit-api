FROM php:8.2-cli

# COMPOSER
COPY --from=composer /usr/bin/composer /usr/bin/composer

# add php extensions
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y git libsodium-dev unzip && \
    docker-php-ext-install sodium sockets

WORKDIR /app
