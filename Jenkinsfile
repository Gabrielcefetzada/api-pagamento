pipeline {
    agent {
        docker {
            // Esta imagem substitui o 'runs-on: ubuntu-latest' + 'setup-php'
            // Ela já contem PHP 8.2, Composer e extensões necessárias
            image 'kirschbaumdevelopment/laravel-test-runner:8.2'
            args '-u root' // Garante permissão para escrever nas pastas
        }
    }

    environment {
        // Define variáveis de ambiente globais para o container
        APP_ENV = 'testing'
    }

    stages {
        stage('Checkout') {
            steps {
                // Baixa o código do repositório (equivalente ao actions/checkout)
                checkout scm
            }
        }

        stage('Prepare Environment') {
            steps {
                script {
                    // Copia o .env (equivalente ao php -r "file_exists...")
                    sh 'cp .env.example .env'
                    
                    // Cria a pasta do banco de dados e o arquivo sqlite
                    sh 'mkdir -p database'
                    sh 'touch database/database.sqlite'
                    
                    // Permissões (ajustado para o ambiente CI)
                    sh 'chmod -R 777 storage bootstrap/cache'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                // Instala dependências do Composer
                sh 'composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist'
            }
        }

        stage('Application Setup') {
            steps {
                // Gera a key
                sh 'php artisan key:generate'
                
                // Limpa cache de configuração para garantir que o .env novo seja lido
                sh 'php artisan config:clear'
            }
        }

        stage('Run Tests') {
            environment {
                // Configuração específica para usar SQLite nos testes
                DB_CONNECTION = 'sqlite'
                DB_DATABASE = 'database/database.sqlite'
            }
            steps {
                // Roda os testes
                sh 'php artisan test'
            }
        }
    }
    
    post {
        always {
            // Limpeza opcional (embora o container docker seja destruído ao fim)
            cleanWs()
        }
    }
}