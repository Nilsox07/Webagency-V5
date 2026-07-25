# Entwicklungsumgebung für SARTU
# Bildet Portal-Lastenheft §1.4 ab: PHP 8.3+, pdo_mysql, sodium, mbstring, intl, fileinfo, openssl
# Der Webserver zeigt ausschließlich auf /public — Portal-Lastenheft §1.3

FROM php:8.3-apache

# Die Entwicklungspakete bleiben absichtlich installiert.
# `apt-get autoremove` würde hier die Laufzeitbibliotheken mitnehmen, gegen die intl und gd
# gebunden sind — das Abbild wäre kleiner und kaputt.
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        unzip \
        git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql intl zip gd opcache \
    && rm -rf /var/lib/apt/lists/*

# sodium ist in den amtlichen PHP-Abbildern normalerweise schon eingebaut.
# Falls nicht, wird es hier nachgezogen, statt den Bau daran scheitern zu lassen.
RUN set -eu; \
    if ! php -m | grep -qix sodium; then \
        apt-get update && apt-get install -y --no-install-recommends libsodium-dev \
        && docker-php-ext-install sodium \
        && rm -rf /var/lib/apt/lists/*; \
    fi

# Prüfung gegen Portal-Lastenheft §1.4.
# Bricht den Bau ab, wenn eine geforderte Erweiterung fehlt — mit klarer Meldung, statt sie
# später im Betrieb zu vermissen.
RUN set -eu; \
    for ext in pdo_mysql sodium mbstring intl fileinfo openssl; do \
        php -m | grep -qix "$ext" || { echo "FEHLENDE PHP-ERWEITERUNG: $ext"; exit 1; }; \
    done; \
    echo "Alle Erweiterungen aus Portal-Lastenheft 1.4 vorhanden"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite headers
COPY .docker/php/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/sartu.ini

WORKDIR /var/www/html
