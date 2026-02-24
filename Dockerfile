FROM dunglas/frankenphp:php8.2

# 1. Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    zip \
    opcache \
    pcntl

# 2. Install Node.js (needed for Vite/Inertia if running in same container)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 4. Copy project files
COPY . .

# 5. Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# 6. Expose ports
EXPOSE 8000
EXPOSE 5173

# 7. Start application
CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8000", "--watch"]

