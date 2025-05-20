# Management-back

Projeto Laravel + Docker para gerenciamento de estoque, produtos, fornecedores, clientes e pedidos.

## Tecnologias

- PHP 8.2 com Laravel 12
- MySQL 8
- Docker e Docker Compose
- Service Layer

## Como executar

### 1. Clone o repositório

```bash
git clone https://github.com/GuDemori/Management-back.git
cd Management-back
```

### 2. Configure o ambiente

```bash
cp .env.example .env
```

### 3. Suba os containers

```bash
docker compose up -d --build
```

### 4. Acesse o container da aplicação

```bash
docker compose exec app bash
```

### 5. Instale as dependências

```bash
composer install
```

### 6. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 7. Execute as migrations

```bash
php artisan migrate
```

### 8. Ative o worker da fila

```bash
php artisan queue:work
```

## Acesso ao banco de dados

Configure sua ferramenta (como Antares SQL ou DBeaver) com os dados abaixo:

- Host: localhost
- Porta: 3306
- Usuário: manager
- Senha: 123456
- Banco: product_management

## Testando a API

Exemplo usando `curl`:

```bash
curl -X POST http://localhost:8000/api/stocks \
  -H "Content-Type: application/json" \
  -d '{"name": "Estoque Central", "description": "Estoque principal da empresa"}'
```

## Estrutura do projeto

```text
app/
├── Domain/
│   └── Stock, Product, Supplier, ...
├── Repositories/
├── Http/
│   └── Controllers/
├── Providers/
```

## Licença

MIT © Gustavo Demori Reichle
