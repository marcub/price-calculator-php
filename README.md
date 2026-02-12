# Sistema de Cálculo de Preços de Produtos

Este projeto é uma implementação de um motor de cálculo de preços para um e-commerce. O sistema foi desenvolvido em **PHP 8.1+** (sem frameworks), com foco estrito em **Clean Architecture**, **SOLID Principles** e **Design Patterns**.

![PHP Version](https://img.shields.io/badge/php-%5E8.1-777BB4?style=flat-square&logo=php&logoColor=white)
![Architecture](https://img.shields.io/badge/architecture-DDD%20%2F%20Clean-orange?style=flat-square)
![Tests](https://img.shields.io/badge/tests-PHPUnit-3776AB?style=flat-square&logo=phpunit&logoColor=white)
![Code Style](https://img.shields.io/badge/code%20style-PSR--12-black?style=flat-square)

## Visão Geral da Arquitetura

O projeto foi estruturado seguindo princípios de **Domain-Driven Design (DDD)** para isolar as regras de negócio da infraestrutura.

### Estrutura de Pastas e Arquivos
```text
.
├── config/                 # Configurações externas (Pricing, Taxes)
├── database/               # Scripts e arquivo do banco de dados
├── src/
│   ├── Domain/
│   │   ├── Cache/          # Interfaces para serviços de cache
│   │   ├── Entity/         # Entidades (Product, Context, Result)
│   │   ├── Factory/        # Encapsulamento da criação de objetos
│   │   ├── Repository/     # Abstração de persistência de dados
│   │   ├── Service/        # Orquestração de fluxos de negócio (Calculadora)
│   │   └── Strategy/       # Algoritmos de precificação (Impostos, Margem)
│   └── Infrastructure/
│       ├── Cache/          # Implementação de FileCache
│       └── Repository/     # Implementação SQLite
├── storage/
│   └── cache/              # Arquivos de cache gerados em tempo de execução
├── tests/                  # Testes Automatizados
│   ├── Integration/        # Testes de Integração
│   └── Unit/               # Testes Unitários
├── docker-compose.yml      # Orquestração de containers
├── Dockerfile              # Definição da imagem PHP
├── phpcs.xml               # Configuração PSR-12
└── phpunit.xml             # Configuração de Testes
```

### Design Patterns Utilizados:
* **Strategy Pattern:** Utilizado para encapsular cada regra de precificação (Impostos, Margem, Descontos). Isso permite adicionar novas regras (ex: nova lei tributária, black friday, cupons, entre outras) sem modificar a classe calculadora, respeitando o *Open/Closed Principle*.
* **Factory Pattern:** (`ProductCalculatorFactory`) Centraliza a criação do calculador e a injeção de dependências (configurações e estratégias).
* **Decorator Pattern:** (`CachedProductCalculator`) Adiciona funcionalidade de Caching ao cálculo de preços, sem poluir a lógica de negócio principal.
* **Repository Pattern:** (`SQLiteProductRepository`) Abstrai a persistência de dados. O domínio desconhece o SQL, dependendo apenas de interfaces.


## Tecnologias e Requisitos

* **PHP 8.1+** (Strict Types enabled)
   * Extensões necessárias: `pdo_sqlite`, `bcmath`.
* **SQLite** (Banco de dados embarcado para facilidade de testes)
* **PHPUnit** (Testes Automatizados)
* **Composer** (Gerenciamento de dependências e Autoload PSR-4)
* **Docker** (Opcional - Containerização do ambiente)


## ⚙️ Instalação e Configuração

### Opção 1: Instalação Local (Requer PHP 8.1+ e Composer)

1. **Instalar dependências:**

   ```bash
   composer install
   ```

2. **Configurar Banco de Dados:**
Execute o script de setup para criar o banco SQLite e popular com produtos de teste:

   ```bash
   php database/setup.php
   ```

3. **Inciar Servidor:**
   ```bash
   php -S localhost:8000
   ```

### Opção 2: Via Docker

Se preferir não configurar o ambiente localmente, utilize o Docker:

   ```bash
   docker compose up -d
   docker compose exec app composer install
   docker compose exec app php database/setup.php
   ```

A API estará disponível em: `http://localhost:8000/api/calculate`

## Como Usar a API

### Endpoint: `POST /api/calculate`

Calcula o preço final de um produto baseando-se no contexto do cliente (Estado, Tipo, Quantidade, Premium/Comum).

#### Payload de Exemplo (Request):

*Nota: O `product_id` refere-se aos produtos gerados no setup (IDs: 1, 2, 3).*

```json
{
    "product_id": 1,
    "context": {
        "quantity": 10,
        "state": "SP",
        "customer_type": "varejo",
        "is_premium": false
    }
}
```

#### Resposta de Exemplo (Response):

A resposta inclui o id do produto, nome do produto, preço final (em centavos e formatado), moeda e, para fins de transparência (observabilidade), a lista de regras que foram aplicadas.

```json
{
    "success": true,
    "data": {
        "product_id": 1,
        "product_name": "Cimento Votorantim 50kg",
        "final_price_cents": 4250,
        "final_price_formatted": "R$ 42,50",
        "currency": "BRL",
        "applied_rules": [
            "Profit margin percentage applied",
            "3% discount for 10-49 items",
            "State tax applied"
        ]
    }
}

```

## Configuração de Regras de Negócio

As margens de lucro e alíquotas de impostos são gerenciadas através do arquivo de configuração:

* Arquivo: `config/pricing.php`

Você pode alterar a margem de lucro (fixa ou porcentagem) e os impostos estaduais neste arquivo sem necessidade de modificar as classes de regra de negócio.

## Testes Automatizados

O projeto possui cobertura de testes unitários e de integração para garantir a integridade dos cálculos.

Para executar a suíte de testes:

```bash
vendor/bin/phpunit
```

ou

```bash
composer test
```

## Padrões de Código

O projeto segue estritamente a **PSR-12** para garantir a consistência e legibilidade do código. A verificação é automatizada via **PHP_CodeSniffer**.

### Como verificar o estilo:
Para checar se o código está em conformidade com a PSR-12:

```bash
composer check-style
```

### Como corrigir automaticamente:

O projeto inclui o `phpcbf` para corrigir automaticamente a maioria dos erros de formatação:

```bash
composer fix-style
```

## Decisões de Projeto

1. **Cache em Arquivo:** Foi implementado um sistema de cache em arquivo (`FileCache`). Em produção, a interface `CacheInterface` permitiria a troca imediata por Redis ou Memcached apenas criando uma nova implementação na camada de Infraestrutura.
2. **Contexto via JSON:** O endpoint recebe dados como `customer_type` e `state` via JSON para facilitar a avaliação técnica e testes de diferentes cenários. Em produção, esses dados seriam hidratados a partir do banco de dados do cliente (via `customer_id`) para garantir segurança e persistência das informações do cliente.
3. **Traceability:** O objeto `CalculationResult` foi introduzido para que o sistema não retorne apenas o valor monetário, mas também o "rastro" de quais regras de negócio afetaram aquele preço, facilitando auditoria e debug.
4. **Money Value Object:** Foi utilizada a biblioteca `moneyphp/money` para garantir precisão decimal. Isso evita erros de arredondamento de ponto flutuante (IEEE 754) críticos em aplicações financeiras.

---