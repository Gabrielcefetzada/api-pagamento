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
        
        stage('Deploy to Render') {
            steps {
                withCredentials([
                    string(credentialsId: 'RENDER_API_TOKEN', variable: 'RENDER_TOKEN'),
                    string(credentialsId: 'RENDER_SERVICE_ID', variable: 'SERVICE_ID')
                ]) {
                    sh """
                    echo "Disparando deploy para o Render Service ID: \$SERVICE_ID"
                    
                    curl --request POST \\
                        --url https://api.render.com/v1/services/\$SERVICE_ID/deploys \\
                        --header 'accept: application/json' \\
                        --header 'authorization: Bearer \$RENDER_TOKEN' \\
                        --header 'content-type: application/json' \\
                        --data '{
                            "clearCache": "do_not_clear"
                        }'
                    """
                }
            }
        }
    }
    
    post {
        always {
            cleanWs()
        }
    }
}