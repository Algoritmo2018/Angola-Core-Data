# Angola Core Data API

API REST para centralizar e disponibilizar dados de referência sobre Angola. O projecto expõe a divisão administrativa do país — **províncias, municípios e comunas** — e informação de **bancos angolanos**, incluindo as respetivas siglas e prefixos bancários.

Foi construída com Laravel e foi pensada para servir aplicações web, móveis e outros serviços que precisem de uma fonte única para estes dados.

## Índice

- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Requisitos](#requisitos)
- [Instalação local](#instalação-local)
- [Execução com Docker](#execução-com-docker)
- [Base de dados e dados iniciais](#base-de-dados-e-dados-iniciais)
- [Utilização da API](#utilização-da-api)
- [Endpoints](#endpoints)
- [Validações e exemplos](#validações-e-exemplos)
- [Testes e qualidade](#testes-e-qualidade)
- [Documentação OpenAPI](#documentação-openapi)
- [Comandos úteis](#comandos-úteis)
- [Estrutura do projecto](#estrutura-do-projecto)
- [Segurança](#segurança)
- [Licença](#licença)

## Funcionalidades

- Consulta paginada e filtrável de províncias, municípios, comunas e bancos.
- Relação geográfica `província → município → comuna`.
- Dados iniciais de províncias e bancos através de *seeders*.
- Criação, actualização, remoção lógica e restauro de registos.
- Lixeira específica para bancos, com opção de remoção permanente.
- Identificadores UUID para os recursos de domínio.
- Documentação OpenAPI e colecção Postman geradas com Scribe.
- Testes de funcionalidade para os endpoints da API.

## Tecnologias

- PHP `^8.3`
- Laravel `^13.8`
- SQLite por padrão (também há configuração para MySQL, MariaDB, PostgreSQL e SQL Server)
- Laravel Sanctum
- PHPUnit 12
- Laravel Pint
- Scribe 5 (documentação de API)
- Docker e Docker Compose
- Vite, Tailwind CSS e Node.js para os recursos de interface incluídos no Laravel

## Requisitos

Para executar localmente, instale:

- PHP 8.3 ou superior, com as extensões `pdo_sqlite`, `mbstring`, `xml`, `zip` e `intl`;
- Composer 2;
- SQLite 3;
- Node.js 20+ e npm (opcional para consumir a API, necessário para os recursos Vite);
- Git.

Como alternativa, use Docker Desktop e Docker Compose.

## Instalação local

Clone o repositório e entre na pasta do projecto:

```bash
git clone <URL_DO_REPOSITORIO>
cd Angola-Core-Data
```

Instale as dependências PHP, prepare o ficheiro de ambiente, crie a chave da aplicação e a base SQLite. No macOS/Linux:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
```

No PowerShell (Windows), substitua os comandos de cópia/criação por:

```powershell
Copy-Item .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
```

Depois, inicie o servidor:

```bash
php artisan serve
```

A API ficará disponível em `http://127.0.0.1:8000/api`.

### Instalação rápida

O comando abaixo instala dependências, cria o `.env` se necessário, gera a chave, executa as migrações e compila os recursos Vite. Para carregar também os dados iniciais, execute o segundo comando.

```bash
composer run setup
php artisan db:seed
```

### Recursos Vite (opcional)

Estes comandos não são necessários para usar os endpoints da API, mas podem ser úteis ao trabalhar com os recursos web do Laravel:

```bash
npm install
npm run dev
```

Para uma compilação de produção:

```bash
npm run build
```

Também pode iniciar o servidor Laravel, a fila e o Vite numa só sessão:

```bash
composer run dev
```

## Execução com Docker

O contentor disponibiliza Apache, PHP 8.4 e SQLite. Na primeira execução, instale as dependências e inicialize a aplicação:

```bash
docker compose up --build -d
docker compose exec app composer install
docker compose exec app sh -lc 'test -f .env || cp .env.example .env'
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Depois aceda a `http://localhost:8000/api/v1/provinces`.

O ficheiro SQLite do contentor é persistido no volume `sqlite-data`. Para parar os contentores:

```bash
docker compose down
```

> Para apagar também os dados persistidos pelo Docker, use `docker compose down -v`. Este comando remove o volume da base de dados.

## Base de dados e dados iniciais

Por padrão, a aplicação usa SQLite. Mantenha no `.env`:

```dotenv
DB_CONNECTION=sqlite
```

Sem `DB_DATABASE` definido, o Laravel usa `database/database.sqlite`. As migrações criam as tabelas de províncias, municípios, comunas e bancos, todas com UUID como chave primária e suporte a remoção lógica (*soft delete*).

O comando `php artisan db:seed` executa:

- `AngolaDataSeeder`: insere as províncias disponíveis no projecto;
- `BankSeeder`: insere a lista inicial de bancos e respetivos prefixos.

Os *seeders* não criam municípios nem comunas. Estes podem ser carregados através dos endpoints `POST` correspondentes ou por novos *seeders*.

Para recriar a base de dados local do zero e voltar a carregar os dados iniciais:

```bash
php artisan migrate:fresh --seed
```

> Atenção: `migrate:fresh` remove todas as tabelas da base de dados configurada. Não o execute sobre dados que pretenda manter.

## Utilização da API

Base URL local:

```text
http://127.0.0.1:8000/api/v1
```

Envie os cabeçalhos abaixo ao fazer pedidos que enviem ou esperem JSON:

```http
Accept: application/json
Content-Type: application/json
```

Neste estado do projecto, os endpoints de domínio não exigem autenticação. O endpoint `GET /api/user` é a excepção e utiliza Laravel Sanctum.

### Paginação e filtros

As listagens aceitam `page` e `per_page`; o padrão é página `1` com `15` itens por página.

```text
GET /api/v1/provinces?page=1&per_page=10&filter=Luanda
GET /api/v1/banks?page=1&per_page=10&bankNameFilter=Investimento
```

Províncias, municípios e comunas usam o parâmetro `filter`, que pesquisa por nome ou UUID. Bancos usam `bankNameFilter` (nome) e `bankIdFilter` (UUID). A listagem da lixeira de bancos aceita `bankNameFilter`.

Exemplo de resposta paginada:

```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Luanda"
    }
  ],
  "meta": {
    "total": 1,
    "is_first_page": true,
    "is_last_page": true,
    "current_page": 1,
    "next_page": 2,
    "previous_page": 0
  }
}
```

## Endpoints

Em todos os caminhos abaixo, substitua `{id}` pelo UUID do recurso.

### Províncias

| Método | Endpoint | Descrição |
| --- | --- | --- |
| `GET` | `/provinces` | Lista províncias activas. Aceita `page`, `per_page` e `filter`. |
| `POST` | `/provinces` | Cria uma província. |
| `PUT` | `/provinces/{id}` | Actualiza o nome de uma província. |
| `DELETE` | `/provinces/{id}` | Move uma província para a lixeira (soft delete). |
| `GET` | `/provinces/show/deleted` | Lista províncias removidas. Aceita paginação e `filter`. |
| `GET` | `/provinces/restore_one/{id}` | Restaura uma província removida. |
| `GET` | `/provinces/restore_all` | Restaura todas as províncias removidas. |

### Municípios

| Método | Endpoint | Descrição |
| --- | --- | --- |
| `GET` | `/municipalities` | Lista municípios activos. Aceita `page`, `per_page` e `filter`. |
| `POST` | `/municipalities` | Cria um município ligado a uma província existente. |
| `PUT` | `/municipalities/{id}` | Actualiza nome e/ou província do município. |
| `DELETE` | `/municipalities/{id}` | Move o município para a lixeira. |
| `GET` | `/municipalities/show/deleted` | Lista municípios removidos. |
| `GET` | `/municipalities/restore_one/{id}` | Restaura um município removido. |
| `GET` | `/municipalities/restore_all` | Restaura todos os municípios removidos. |

### Comunas

| Método | Endpoint | Descrição |
| --- | --- | --- |
| `GET` | `/comunes` | Lista comunas activas. Aceita `page`, `per_page` e `filter`. |
| `POST` | `/comunes` | Cria uma comuna ligada a um município existente. |
| `PUT` | `/comunes/{id}` | Actualiza nome e/ou município da comuna. |
| `DELETE` | `/comunes/{id}` | Move a comuna para a lixeira. |
| `GET` | `/comunes/show/deleted` | Lista comunas removidas. |
| `GET` | `/comunes/restore_one/{id}` | Restaura uma comuna removida. |
| `GET` | `/comunes/restore_all` | Restaura todas as comunas removidas. |

### Bancos

| Método | Endpoint | Descrição |
| --- | --- | --- |
| `GET` | `/banks` | Lista bancos activos. Aceita `page`, `per_page`, `bankNameFilter` e `bankIdFilter`. |
| `POST` | `/banks` | Cadastra um banco. |
| `PUT` | `/banks/{id}` | Actualiza parcialmente os dados de um banco. |
| `DELETE` | `/banks/{id}` | Move o banco para a lixeira. |
| `GET` | `/banks/flat/trash/can` | Lista bancos na lixeira. Aceita `page`, `per_page` e `bankNameFilter`. |
| `PUT` | `/banks/recover/one/{id}` | Recupera um banco da lixeira. |
| `GET` | `/banks/restore/all` | Recupera todos os bancos da lixeira. |
| `DELETE` | `/banks/permanently/delete/{id}` | Elimina definitivamente um banco. |

## Validações e exemplos

### Criar uma província

`name` é obrigatório, deve ser texto entre 1 e 255 caracteres e único.

```bash
curl -X POST http://127.0.0.1:8000/api/v1/provinces \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name":"Luanda"}'
```

### Criar um município

`name` e `province_id` são obrigatórios. A província indicada precisa existir.

```bash
curl -X POST http://127.0.0.1:8000/api/v1/municipalities \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name":"Viana","province_id":"UUID_DA_PROVINCIA"}'
```

### Criar uma comuna

`name` e `municipality_id` são obrigatórios. O município indicado precisa existir.

```bash
curl -X POST http://127.0.0.1:8000/api/v1/comunes \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name":"Estalagem","municipality_id":"UUID_DO_MUNICIPIO"}'
```

### Criar um banco

Os campos `bank_name`, `short_name`, `country_prefix` e `bank_prefix` são obrigatórios. Nome, sigla e prefixo bancário devem ser únicos; `bank_prefix` deve ter exactamente quatro caracteres.

```bash
curl -X POST http://127.0.0.1:8000/api/v1/banks \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "bank_name":"Banco Exemplo de Angola",
    "short_name":"BEA",
    "country_prefix":"AO06",
    "bank_prefix":"0099"
  }'
```

Uma criação de banco bem-sucedida responde com `201 Created` e inclui `success`, `message` e `data`. As validações inválidas devolvem `422 Unprocessable Entity`, com os campos em erro no objecto `errors`.

### Actualização e remoção

As actualizações de municípios, comunas e bancos aceitam somente os campos que pretende alterar. Para províncias, envie `name` no corpo do pedido. As remoções comuns são lógicas, logo os registos podem ser consultados na lixeira e restaurados.

```bash
curl -X PUT http://127.0.0.1:8000/api/v1/banks/UUID_DO_BANCO \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"short_name":"NOVO"}'

curl -X DELETE http://127.0.0.1:8000/api/v1/banks/UUID_DO_BANCO \
  -H "Accept: application/json"
```

> `DELETE /banks/permanently/delete/{id}` não pode ser desfeito. Use-o apenas depois de confirmar que o banco já se encontra na lixeira e não deve ser recuperado.

## Testes e qualidade

Os testes de funcionalidade utilizam SQLite em memória; não alteram a base de dados de desenvolvimento.

```bash
php artisan test
```

Ou, para limpar a configuração antes de testar:

```bash
composer test
```

Para formatar o código PHP com Laravel Pint:

```bash
./vendor/bin/pint
```

No Windows/PowerShell:

```powershell
./vendor/bin/pint.bat
```

## Documentação OpenAPI

O projecto inclui Scribe para gerar documentação interactiva, colecção Postman e especificação OpenAPI:

```bash
php artisan scribe:generate
```

Com o servidor iniciado, consulte:

- Interface: `http://127.0.0.1:8000/docs`
- OpenAPI: `http://127.0.0.1:8000/docs.openapi`
- Colecção Postman: `http://127.0.0.1:8000/docs.postman`

Gere novamente a documentação sempre que alterar rotas, validações ou respostas.

## Comandos úteis

| Objectivo | Comando |
| --- | --- |
| Ver todas as rotas | `php artisan route:list` |
| Ver apenas as rotas API | `php artisan route:list --path=api` |
| Executar migrações | `php artisan migrate` |
| Executar *seeders* | `php artisan db:seed` |
| Reinicializar a BD com dados iniciais | `php artisan migrate:fresh --seed` |
| Limpar caches | `php artisan optimize:clear` |
| Executar testes | `php artisan test` |
| Gerar documentação | `php artisan scribe:generate` |
| Iniciar servidor local | `php artisan serve` |
| Iniciar com Docker | `docker compose up --build -d` |
| Ver registos do Docker | `docker compose logs -f app` |

## Estrutura do projecto

```text
app/
├── Adapters/          # Adaptação das respostas paginadas
├── DTOs/              # Objectos de transferência de dados
├── Http/
│   ├── Controllers/   # Controladores da API
│   ├── Requests/      # Validações dos pedidos
│   └── Resources/     # Transformação das respostas JSON
├── Models/            # Province, Municipality, Comune e Bank
├── Repositories/      # Acesso e paginação dos dados
└── Services/          # Regras de negócio
database/
├── migrations/        # Estrutura das tabelas
└── seeders/           # Dados iniciais de Angola e bancos
routes/
├── api.php            # Agregador das rotas da API
├── province_rooter/
├── municipality_rooter/
├── comune_rooter/
└── bank_rooter/
tests/Feature/Api/     # Testes dos endpoints
```

## Segurança

Os endpoints de gestão (`POST`, `PUT` e `DELETE`) estão públicos na configuração actual. Antes de disponibilizar a API em produção, proteja-os com autenticação/autorização (por exemplo, Laravel Sanctum), active HTTPS, configure `APP_ENV=production` e `APP_DEBUG=false`, e restrinja as origens CORS de acordo com os consumidores autorizados.

## Licença

O `composer.json` declara a licença MIT para o projecto.
