# Management-back

Projeto backend desenvolvido em **Laravel**, com ambiente Docker completo para facilitar a instalação, desenvolvimento e colaboração.

---

## 🧰 Pré-requisitos

Você precisa ter instalado em sua máquina:

- [Git](https://git-scm.com/)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Composer](https://getcomposer.org/)
- [PHP 8.2 ou superior (apenas se for rodar Laravel fora do Docker)]

---

## 🖥️ Como rodar o projeto (Linux ou Windows)

No Linux (Ubuntu) ou no Windows (usando WSL ou Git Bash), siga os passos abaixo exatamente nessa ordem para configurar o ambiente e rodar o projeto:

```bash
# Atualiza os pacotes do sistema
sudo apt update && sudo apt upgrade -y

# Instala PHP e extensões necessárias para o Laravel
sudo apt install -y php php-cli php-mbstring php-xml php-bcmath php-curl php-zip php-mysql php-tokenizer php-pgsql php-sqlite3 php-gd php-intl php-soap php-readline php-common unzip curl git

# Instala o Docker e o Docker Compose
sudo apt install docker.io docker-compose -y

# Inicia e habilita o Docker para iniciar com o sistema
sudo systemctl enable docker
sudo systemctl start docker

# Instala o Composer (gerenciador de dependências PHP)
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
composer --version

# Clona o repositório do projeto
git clone https://github.com/GuDemori/Management-back.git
cd Management-back

# Sobe os containers com Docker (Laravel + MySQL)
docker-compose up -d --build

# Acessa o container Laravel
docker exec -it laravel-app bash

# Instala as dependências do Laravel dentro do container
composer install

# Copia o arquivo de exemplo de ambiente e gera a chave da aplicação
cp .env.example .env
php artisan key:generate

# (Opcional) Executa as migrations para criar as tabelas no banco
php artisan migrate