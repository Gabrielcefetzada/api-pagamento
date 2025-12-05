pipeline {
    agent {
        docker {
            image 'kirschbaumdevelopment/laravel-test-runner:8.2'
            args '-u root'
        }
    }

    environment {
        APP_ENV = 'testing'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Prepare Environment') {
            steps {
                script {
                    sh 'cp .env.example .env'
                    
                    sh 'mkdir -p database'
                    sh 'touch database/database.sqlite'
                    
                    sh 'chmod -R 777 storage bootstrap/cache'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist'
            }
        }

        stage('Application Setup') {
            steps {
                sh 'php artisan key:generate'
                
                sh 'php artisan config:clear'
            }
        }

        stage('Run Tests') {
            environment {
                DB_CONNECTION = 'sqlite'
                DB_DATABASE = 'database/database.sqlite'
            }
            steps {
                sh 'php artisan test'
            }
        }
    }
    
    post {
        always {
            cleanWs()
        }
    }
}