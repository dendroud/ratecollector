FROM php:8.1

WORKDIR /code

RUN set -ex;\
        apt-get update; \
        apt-get install -y --no-install-recommends  \
        mc wget git telnet libpq-dev procps build-essential chrpath; 
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql 
RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis
