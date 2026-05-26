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
# បើក PDO MySQL, string handling, image, background process, math, GD image
RUN docker-php-ext-install \
    pdo_mysql mbstring exif pcntl bcmath gd

# ================= MongoDB Extension (បើប្រើ MongoDB) =================
RUN pecl install mongodb && docker-php-ext-enable mongodb

# ================= ដំឡើង Composer =================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ================= កំណត់ទីតាំង project =================
WORKDIR /var/www/html

# copy code ទាំងអស់ចូល container
COPY . .

# ================= ដំឡើង Laravel dependencies =================
RUN composer install --no-dev --optimize-autoloader

# ================= clear cache Laravel =================
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# ================= កំណត់ permission =================
# អោយ storage និង cache អាច write បាន
RUN chown -R www-data:www-data /var/www/html/storage \
    /var/www/html/bootstrap/cache

# ================= បើក port សម្រាប់ Render =================
EXPOSE 10000

# ================= run Laravel server =================
CMD php artisan serve --host=0.0.0.0 --port=10000