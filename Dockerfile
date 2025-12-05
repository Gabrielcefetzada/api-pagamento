FROM php:8.3-cli

# Instalar dependências mínimas
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Diretório de trabalho
WORKDIR /var/www/html

# Copiar tudo
COPY . .

# Instalar dependências
RUN composer install --no-dev --optimize-autoloader

# Gerar key se necessário
RUN if [ ! -f .env ]; then \
    cp .env.example .env && \
    php artisan key:generate; \
    fi

# Expoe porta dinâmica (Render usa PORT)
EXPOSE $PORT

# Comando simples
CMD php artisan serve --host=0.0.0.0 --port=$PORT