pipeline {
    agent any
    
    environment {
        REPO_PATH = 'C:\\xampp\\htdocs\\SERVIDOR'
        GIT_REPO = 'git@github.com:AlejMtz/SERVIDOR.git'
        REMOTE_USER = 'mapache'
        REMOTE_HOST = '192.168.3.3'
    }
    
    stages {
        stage('Clone Code from Windows') {
            steps {
                script {
                    // Comprimir el código en la máquina Windows y copiarlo a la máquina virtual
                    sh """
                    ssh ${REMOTE_USER}@${REMOTE_HOST} 'powershell Compress-Archive -Path ${REPO_PATH} -DestinationPath C:\\xampp\\htdocs\\repositorio.zip'
                    scp ${REMOTE_USER}@${REMOTE_HOST}:C:\\xampp\\htdocs\\repositorio.zip .
                    unzip repositorio.zip -d ./repositorio
                    """
                }
            }
        }
        
        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv('SonarQube') {
                    sh 'sonar-scanner -Dsonar.projectKey=tu_proyecto -Dsonar.sources=./repositorio'
                }
            }
        }
        
        stage('Push to Git') {
            steps {
                script {
                    // Configurar Git y subir los cambios a la rama main
                    sh """
                    cd ./repositorio
                    git init
                    git remote add origin ${GIT_REPO}
                    git add .
                    git commit -m 'Automated commit'
                    git branch -M main
                    git push -u origin main
                    """
                }
            }
        }
    }
    
    post {
        always {
            // Limpiar archivos temporales
            cleanWs()
        }
    }
}
