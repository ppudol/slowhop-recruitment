FROM php:8.3-fpm

RUN apt update
RUN apt install -y zip unzip vim

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /tmp
RUN chmod -R g+rw /tmp

WORKDIR /application

ADD . /application
