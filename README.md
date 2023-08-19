# Título do projeto

Aplicação encurtador de links.

<br>

## 🚀 Começando
---
<br>
Essas instruções permitirão que você obtenha uma cópia do projeto em operação na sua máquina local para fins de desenvolvimento e teste.

<br>

### 📋 Pré-requisitos

```
Git, Docker e Docker Compose
```
<br>

### 🔧 Instalação

Siga este passo-a-passo, para ter um ambiente de desenvolvimento em execução.

```
1. Efetue o clone do projeto, utilizando seu terminal:
    git clone git@github.com:ElessandroPrestes/link-shortener.git

2. Acesse a pasta do projeto clonado:
    link-shortener

3. Apague histórico do repositorio:
    rm -rf .git

4. Gere o arquivo  .env, com o seguinte comando:
    cp .env.example .env

5. Faça o deployment da aplicação utilizando containers, seguindo as instruções a seguir:
    docker compose up -d --build

Aguarde a finalização.

6. Acesse o container app, e acesse o bash:
    docker compose exec app bash

7. Instale as dependências via composer:
    composer install

8. Gere a chave key , necessária pelo serviço de criptografia Illuminate:
    php artisan key:generate

9. Execute as migrations :
    php artisan migrate

10. Execute os Seeds
    php artisan db:seed

```

<br>

## ⚙️ Executando os testes

<br>

### ⌨️ Ainda no seu terminal, dentro do container app,  execute os scripts abaixo:

```
OBS : Caso tenha saido do container, repita o passo 6 da instalação.

1.  php artisan test

2.  Para sair do container app, execute CTRL + D.
```

<br>

### Acesse no seu browser:

```
Documentação da API

L5-Swagger - Packpage
http://localhost:8000/api/documentation

Adminer - Ferramenta de gerenciamento de banco de dados

    http://localhost:8080
    servidor : db-mysql
    user     : root
    password : root
    database : laravel

Laravel telescope - Para uma melhor análise, das informações obtidas atraves da aplicação.

    http://localhost:8000/telescope
```

<br>

## 🛠️ Construído com

Tecnologias Utilizadas:

* [PHP](https://www.php.net/docs.php) - Linguagem de programação
* [Laravel](https://laravel.com/docs/10.x) - Laravel é um framework PHP livre e open-source
* [Nginx](https://docs.nginx.com/) - Servidor web
* [Mysql](https://dev.mysql.com/doc/) - Sistema de gerenciamento de banco de dados
* [Redis](https://redis.io/docs/) - Redis é um armazenamento de estrutura de dados em memória
* [Adminer](https://www.adminer.org/) - Ferramenta de gerenciamento de banco de dados
* [Docker](https://docs.docker.com/) - Software usado para implantar aplicativos dentro de containers virtuais.
* [Docker Compose](https://docs.docker.com/compose/) - Ferramenta para a criação e execução de múltiplos containers de aplicação.

## ✒️ Autor

* **Elessandro Prestes Macedo** 

## 📄 Licença

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


