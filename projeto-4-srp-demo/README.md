# Estacionamento (PHP 8.2+, SQLite, PSR-4)

Sistema modular seguindo SOLID, DRY, KISS e boas práticas de Clean Code, organizado em camadas `Domain`, `Application` e `Infra`.

## Funcionalidades

- Cadastrar entrada e saída de veículos
- Calcular tarifas por hora conforme tipo de veículo
- Relatório de uso e faturamento por tipo
- Banco SQLite automático na pasta `storage/`
- Interface mínima em HTML (Tailwind via CDN)

## Regras de Negócio

- Tipos: carro (`car`), moto (`motorcycle`), caminhão (`truck`)
- Tarifas: carro R$ 5/h, moto R$ 3/h, caminhão R$ 10/h
- Tempo em horas arredondado para cima
- Relatório: total de veículos e faturamento por tipo

## Arquitetura

- `src/Domain`: entidades e contratos (ex.: `Vehicle`, `ParkingSession`, `PricingStrategy`)
- `src/Application`: casos de uso/serviços e interfaces (ex.: `CheckInService`, `CheckOutService`, `ReportService`, `SessionRepository`)
- `src/Infra`: implementação técnica (ex.: `SQLiteSessionRepository`, `Connection`, `Migration`)
- `public/`: interface web (`index.php`)

## Mapeamento SOLID

- SRP: classes com uma responsabilidade (ex.: `CheckOutService` só orquestra checkout)
- OCP: novos tipos de veículo via nova `PricingStrategy` sem mudar lógica existente
- LSP: todas estratégias de preço substituem `PricingStrategy`
- ISP: repositório e precificação separados (`SessionRepository`, `PricingStrategy`)
- DIP: serviços dependem de interfaces (`SessionRepository`) e não de implementações

## Como rodar

1. Requisitos: PHP 8.2+ instalado
2. Clonar o projeto
3. Iniciar servidor embutido:

   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

4. Abrir `http://127.0.0.1:8000` no navegador

Observação: O autoload próprio (`autoload.php`) já está configurado. Composer é opcional.

## Banco de Dados

- Primeira execução cria `storage/database.sqlite` e migra a tabela `parking_sessions` automaticamente.

## GUI

- Formulário para entrada (placa e tipo)
- Lista de sessões ativas com botão de saída
- Relatório acumulado por tipo (quantidade e faturamento)

## Extensibilidade (novo tipo de veículo)

1. Criar nova estratégia `src/Domain/Pricing/NovoTipoPricingStrategy.php` implementando `PricingStrategy`
2. Registrar no `PricingService` adicionando o tipo na lista de estratégias

## Divisão de trabalho (4 integrantes)

- Domain: entidades e enum de tipos
- Application: serviços (check-in, check-out, relatório)
- Infra: repositório SQLite e migração
- Frontend: HTML/Tailwind e fluxo de formulário

## Entregáveis

- Código em repositório GitHub
- Este README com estrutura e instruções
- Demonstração (vídeo curto ou prints)
- Apresentação breve (5 minutos)