FROM php:8.3-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/htdocs

RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
		libzip-dev \
		unzip \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j"$(nproc)" gd intl mysqli opcache zip \
	&& a2enmod rewrite \
	&& rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html

RUN rm -f /var/www/html/htdocs/conf/conf.php \
	&& mkdir -p /var/www/html/documents \
	&& chown -R www-data:www-data /var/www/html/htdocs/conf /var/www/html/documents \
	&& find /var/www/html/htdocs/conf /var/www/html/documents -type d -exec chmod 775 {} \;

RUN sed -ri \
	-e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" \
	/etc/apache2/sites-available/000-default.conf \
	/etc/apache2/apache2.conf

EXPOSE 80
