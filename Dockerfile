# Dockerfile com SQLite (mais simples)
FROM php:8.3-cli

# Instalar dependências + SQLite
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar tudo
COPY . .

# Instalar dependências
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Criar arquivo SQLite
RUN touch database/database.sqlite

# Configurar .env para SQLite automaticamente
RUN if [ ! -f .env ]; then \
    cp .env.example .env && \
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env && \
    sed -i '/DB_HOST=/d' .env && \
    sed -i '/DB_PORT=/d' .env && \
    sed -i '/DB_DATABASE=/d' .env && \
    sed -i '/DB_USERNAME=/d' .env && \
    sed -i '/DB_PASSWORD=/d' .env && \
    echo 'DB_DATABASE=database/database.sqlite' >> .env && \
    echo 'SESSION_DRIVER=file' >> .env && \
    php artisan key:generate --force; \
    fi

# Rodar migrations (opcional)
RUN php artisan migrate --force

EXPOSE ${PORT:-8000}

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}