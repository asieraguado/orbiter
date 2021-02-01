FROM php:7.4.14-apache-buster
RUN a2enmod rewrite
RUN /etc/init.d/apache2 restart
