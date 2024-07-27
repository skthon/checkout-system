FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip

COPY . /checkout-system
WORKDIR /checkout-system

COPY --from=composer /usr/bin/composer /usr/bin/composer