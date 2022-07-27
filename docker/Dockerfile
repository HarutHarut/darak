FROM php:8.0-apache
RUN a2enmod rewrite
RUN docker-php-ext-install pdo pdo_mysql
ADD /docker/apache.conf /etc/apache2/sites-available/luglockers.conf
ADD /docker/mysqlconf.cnf /etc/mysql/conf.d/mysqlconf.cnf
RUN a2ensite luglockers.conf
RUN a2dissite 000-default.conf
RUN service apache2 restart

RUN apt-get update
RUN apt-get install -y curl git unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get install -y libpq-dev
RUN apt-get install -y zlib1g-dev libzip-dev libpng-dev
RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libgd-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
