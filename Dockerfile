##################
# Build Stage 1/4   -> Dependencies
##################
FROM php:8.1-fpm AS dependencies

# Linux distro's requires a correct UID:GID mapping in order to work with host volumes
ARG USER_ID=1000
ARG GROUP_ID=1000

EXPOSE 8080
WORKDIR /opt/application

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_NO_INTERACTION=1

# Install Dependencies
RUN apt-get update && apt-get install -y supervisor nginx unzip libicu-dev bc netcat-openbsd sudo zlib1g-dev libzip-dev && apt-get clean

RUN docker-php-ext-install intl pdo_mysql opcache zip sockets && \
    pecl install xdebug apcu && docker-php-ext-enable apcu sockets

COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer
COPY .docker/entrypoint.sh /usr/bin/entrypoint.sh
RUN chmod +x /usr/bin/entrypoint.sh

# Create a non-root user
RUN addgroup --gid $GROUP_ID webuser && \
    adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID webuser && \
    passwd -d webuser && \
    echo 'webuser ALL=(ALL:ALL) NOPASSWD: ALL' > /etc/sudoers

# Correct permissions for non-root operations
RUN chown -R webuser:webuser \
    /run \
    /var/log/nginx \
    /var/lib/nginx \
    /opt/application

## Configure
RUN ln -s /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .docker/supervisord /etc/supervisor/conf.d/
RUN chmod +x /etc/supervisor/conf.d/stop-supervisor.sh /etc/supervisor/conf.d/wait-until-php-start.sh
COPY .docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY .docker/php/ext-xdebug.ini /usr/local/etc/php/conf.d/ext-xdebug.ini

USER webuser

COPY composer.* ./
RUN composer install --no-scripts --no-autoloader

ENTRYPOINT ["/usr/bin/entrypoint.sh"]

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping