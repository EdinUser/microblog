FROM php:8.0-apache

RUN apt-get update
RUN apt-get install -y libz-dev libicu-dev zip unzip && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl

RUN docker-php-ext-install mysqli

RUN apt-get update && apt-get install -y libmagickwand-dev && \
    pecl install imagick && \
    docker-php-ext-enable imagick

RUN docker-php-ext-install exif
RUN docker-php-ext-install sockets

COPY vhost.conf /etc/apache2/sites-available/microblog.local.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN rm -f /var/run/apache2/apache2.pid

RUN a2dissite 000-default.conf
RUN a2ensite microblog.local.conf

RUN chown -R www-data:www-data /var/www
USER www-data

#RUN composer update -0
EXPOSE 80

# Start Apache
#CMD ["/usr/sbin/httpd","-D","FOREGROUND"]
