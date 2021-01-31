FROM php:apache-buster
RUN a2enmod rewrite
RUN /etc/init.d/apache2 restart
