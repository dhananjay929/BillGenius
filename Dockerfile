FROM php:8.2-apache

# Enable mod_rewrite for .htaccess routing
RUN a2enmod rewrite

# Allow .htaccess overrides in Apache
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Install mbstring (used by some DISCOM functions)
RUN docker-php-ext-install mbstring

# Copy all project files into the web root
COPY . /var/www/html/

# Create writable folders and set correct ownership
RUN mkdir -p /var/www/html/download /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod 755 /var/www/html/download /var/www/html/logs

EXPOSE 80

CMD ["apache2-foreground"]
