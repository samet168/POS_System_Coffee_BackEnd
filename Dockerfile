# FROM php:8.3-fpm

# # ដំឡើង System Dependencies
# RUN apt-get update && apt-get install -y \
#     libpng-dev libonig-dev libxml2-dev zip unzip git curl libcurl4-openssl-dev pkg-config libssl-dev

# # ដំឡើង PHP Extensions ធម្មតា និង MongoDB Extension
# RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
# RUN pecl install mongodb && docker-php-ext-enable mongodb

# # ដំឡើង Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # កំណត់ទីតាំងកូដ
# WORKDIR /var/www/html
# COPY . .

# # ដំឡើង Library (Dependencies)
# RUN composer install --no-dev --optimize-autoloader

# # បើកសិទ្ធិឱ្យ Folder Storage (សំខាន់សម្រាប់ Render)
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# # បើក Port 10000 (Port ស្ដង់ដារបស់ Render)
# EXPOSE 10000

# # បញ្ជាឱ្យ Run Server
# CMD php artisan serve --host=0.0.0.0 --port=10000

# ប្រើ PHP version 8.3 ជា base image
FROM php:8.3-fpm

# ================= ដំឡើង System Dependencies =================
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    libcurl4-openssl-dev pkg-config libssl-dev

# ================= ដំឡើង PHP Extensions =================
RUN docker-php-ext-install \
    pdo_mysql mbstring exif pcntl bcmath gd

# ================= MongoDB Extension =================
RUN pecl install mongodb && docker-php-ext-enable mongodb

# ================= ដំឡើង Composer =================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ================= កំណត់ទីតាំង project =================
WORKDIR /var/www/html

# ចម្លងកូដចូល container
COPY . .

# ================= ដំឡើង Laravel dependencies =================
# បន្ថែម --no-interaction ដើម្បីកុំឱ្យវាទាមទារការចុច Yes/No ពេល build
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ================= កំណត់ permission =================
RUN chown -R www-data:www-data /var/www/html/storage \
    /var/www/html/bootstrap/cache

# ================= បើក port សម្រាប់ Render =================
EXPOSE 10000

# ================= Run Laravel Server =================
# យើងដាក់ឱ្យវា Optimize និង Clear Cache មុនពេលរត់ Server តែម្តង
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=10000