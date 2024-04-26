FROM php:8.3-cli
RUN apt update \
    && apt install -y git zip unzip nodejs npm \
    && docker-php-ext-install pdo_mysql opcache \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt clean && rm -rf /var/lib/apt/lists/*
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

COPY startup.sh /startup.sh
RUN chmod 744 /startup.sh

WORKDIR /pkg

CMD ["/startup.sh"]
