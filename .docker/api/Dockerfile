FROM php:8.3-cli
RUN apt update \
    && apt install -y git zip unzip \
    && docker-php-ext-install pdo_mysql opcache \
    && apt clean && rm -rf /var/lib/apt/lists/* \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

COPY startup.sh /startup.sh
RUN chmod 744 /startup.sh

WORKDIR /pkg/api

CMD ["/startup.sh"]
