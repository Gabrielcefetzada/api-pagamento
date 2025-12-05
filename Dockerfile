# ----------------------------------------------------------------------
# ESTÁGIO 1: BUILDER (Compilação de Assets e Dependências)
# ----------------------------------------------------------------------
FROM php:8.3-cli AS builder

# Variáveis do Projeto (para usar o mesmo Node que o Sail usa)
ARG NODE_VERSION=20
ARG COMPOSER_ARGS="--no-dev --optimize-autoloader"

WORKDIR /app

# 1. Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    sqlite3 \
    libsqlite3-dev \
    libpq-dev \
    libzip-dev \
    libmemcached-dev \
    # Dependências Node
    && curl -sL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get install -y nodejs \
    # Limpeza
    && rm -rf /var/lib/apt/lists/*

# 2. Instala extensões PHP necessárias para o Laravel
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets zip

# 3. Instala e configura o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 4. Copia todo o código da raiz para o container
COPY . /app

# 5. Instala dependências do PHP (Com "no-dev" para produção)
RUN composer install ${COMPOSER_ARGS}

# 6. Compila Assets (Remover se a API for puramente backend e não usar JS/CSS)
# RUN npm install && npm run build


# ----------------------------------------------------------------------
# ESTÁGIO 2: PRODUCTION (Ambiente de Execução Final)
# ----------------------------------------------------------------------

# Usa a imagem PHP-FPM (mais leve e ideal para produção)
FROM php:8.3-fpm-alpine AS production

# 1. Instala dependências de runtime
RUN apk add --no-cache \
    nginx \
    bash \
    curl \
    git \
    libzip \
    libpng \
    libpq \
    libwebp \
    libjpeg-turbo \
    # Instala extensões de runtime
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets zip \
    && apk add --no-cache tzdata \
    && cp /usr/share/zoneinfo/UTC /etc/localtime \
    && echo "UTC" > /etc/timezone

# 2. Cria o usuário não-root 'www' (melhor segurança)
RUN adduser -D -u 1000 www

WORKDIR /var/www/html

# 3. Copia o código final do estágio 'builder'
COPY --from=builder /app /var/www/html

# 4. Ajusta as permissões de pastas críticas
RUN chown -R www:www /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# 5. Copia e configura o arquivo nginx (o Render usará isso)
# Assume que 'default.conf' está na RAIZ, ao lado deste Dockerfile
COPY default.conf /etc/nginx/http.d/default.conf

# 6. Define a porta e o comando de inicialização
EXPOSE 80

# Comando de entrada: Inicia o PHP-FPM e o Nginx
CMD ["/bin/bash", "-c", "php-fpm && nginx -g 'daemon off;'"]