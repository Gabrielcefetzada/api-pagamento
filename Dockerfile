# ----------------------------------------------------------------------
# ESTÁGIO 1: BUILDER (Compilação de Assets e Dependências)
# ----------------------------------------------------------------------
FROM php:8.3-cli AS builder

# Variáveis do Projeto (Definidas para compatibilidade e flexibilidade)
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
    # Instala Node.js para compilação de assets
    && curl -sL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get install -y nodejs \
    # Limpeza
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instala extensões PHP necessárias para o Laravel
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets zip

# 3. Instala e configura o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 4. Copia o código-fonte da raiz
COPY . /app

# 5. Instala dependências do PHP (Com "no-dev" para produção)
RUN composer install ${COMPOSER_ARGS}

# Se sua API for puramente backend, você pode OMITIR este bloco.
# RUN npm install && npm run build


# ----------------------------------------------------------------------
# ESTÁGIO 2: PRODUCTION (Ambiente de Execução Final)
# ----------------------------------------------------------------------

# Usa a imagem PHP-FPM Alpine (muito mais leve) para servir a aplicação
FROM php:8.3-fpm-alpine AS production

# 1. Instala dependências de runtime e de desenvolvimento necessárias para as extensões
RUN apk add --no-cache \
    nginx \
    bash \
    curl \
    git \
    tzdata \
    # Dependências de Extensão que precisam ser instaladas novamente no Alpine
    libzip-dev \
    libpng-dev \
    libpq-dev \
    libxml2-dev \
    libjpeg-turbo-dev \
    # Instalação das Extensões
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets zip \
    \
    # Limpeza: Remove pacotes de desenvolvimento para manter a imagem pequena e segura
    && apk del \
    libzip-dev \
    libpng-dev \
    libpq-dev \
    libxml2-dev \
    libjpeg-turbo-dev \
    \
    # Configuração de Fuso Horário
    && cp /usr/share/zoneinfo/UTC /etc/localtime \
    && echo "UTC" > /etc/timezone \
    \
    # Limpeza final
    && rm -rf /var/cache/apk/*

# 2. Cria o usuário não-root 'www' (melhor segurança)
RUN adduser -D -u 1000 www

WORKDIR /var/www/html

# 3. Copia o código final do estágio 'builder'
COPY --from=builder /app /var/www/html

# 4. Ajusta as permissões de pastas críticas
RUN chown -R www:www /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# 5. Copia o arquivo de configuração Nginx (deve estar na raiz)
COPY default.conf /etc/nginx/http.d/default.conf

# 6. Define a porta
EXPOSE 80

# 7. Comando de entrada: Inicia o PHP-FPM e o Nginx
CMD ["/bin/bash", "-c", "php-fpm && nginx -g 'daemon off;'"]