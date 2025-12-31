
**Configuraração e Setup do Projeto**

**Arquivo .env**

A fins de praticidade e por existir apenas localmente, basta copiar o .env.example, criar um

novo .env e copiar o conteúdo daquele para este. A única coisa que deve ser preenchida é a variável APP\_KEY. Para siga o próximo passo primeiro.

**Start no servidor**

O projeto roda com Docker e Docker Compose, com 4 contâiners – Aplicação Laravel, Mailpit, Mysql e Redis.

Execute:

```shell
sudo docker compose up
```

ou se já tiver configurado um grupo para o Docker: docker compose up

Entre no contâiner do laravel:

```shell
sudo docker exec -it api-pagamento-laravel.test-1 bash Execute:
```

```shell
php artisan composer install

php artisan key:generate

php artisan migrate:fresh --seed
```

Geramos um valor para a env var APP\_KEY e rodamos as migrações necessários do banco e o populamos com alguns usuários, criação de carteira, e permissões.

**Usuários**

Sobre os usuários cadastrados temos

Um usuário comum. Inclusive é o único que possui saldo por padrão ($100). Login →
```json
{
  "email": "commonUser@example.com", 
  "password": "password"
}  
```

Um lojista
```json
{
  "email": "storekeeper@example.com",
  "password": "password"
}
```
Um admin
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```
**Fila de e-mails**

Quando se cria uma transação, um job de envio de e-mail é disparado na fila padrão. Para deixar o worker da fila aberto basta rodar em outra aba do terminal, dentro do container:

```shell
php artisan queue:work
```
**Endpoints**

A collection com os endpoints da api estão dentro da pasta docs/requests-collection.json. Mas além disso, a escolha do client api para testar os endpoints foi o Bruno. Bruno é um cliente de API Opensource, gratuito, rápido e compatível com Git, que visa revolucionar o status quo representado por Postman, Insomnia e ferramentas similares por aí. Bruno armazena suas coleções diretamente em uma pasta no seu sistema de arquivos. Usam uma linguagem de marcação de texto simples, Bru, para salvar informações sobre solicitações de API. Você pode usar o git ou qualquer controle de versão de sua escolha para colaborar em suas coleções de API.

A pasta do Bruno no projeto é a /requests-collection

**Testes**

Na aplicação, existem testes de Unidade e de Feature. Para rodar os testes, basta entrar no container e executar:

```shell
php artisan test
```
**CI/CD**

A aplicação contém uma simples pipe que roda todos os testes antes de uma PR para a master ou um push para master. Localizada em: **.github/workflows/pipeline.yml**

Para fins de prova de conceito, posteriormente foi adicionado im JenkinsFile com o intuito de buildar a aplicação, rodar testes e fazer deploy no Render. A automação já foi desprovisionada tanto no Jenkins quanto no Render.
