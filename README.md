## Desafio Beepay

### Requisitos 

Para rodar o projeto é necessário ter os seguintes aplicativos instalados e atualizados:
- Docker ^25.0.3
- Docker-compose ^2.24.6
- Composer ^2.7.1

### Instalando o projeto

Instalação de dependências
```
composer install
```

Crie o arquivo .env com uma cópia do .env.example
```
cp .env.example .env
```

Use o seguinte comando para construir as imagem dos containers necessários para rodar o projeto:
```
./vendor/bin/sail build --no-cache
```
___
### Rodando o projeto

Para rodar o projeto basta executar o seguinte comando:
```
./vendor/bin/sail up -d && artisan horizon
```
Após iniciado acesse [http://localhost]() para verificar se a API está online.
___
### Rodando migrations e seeders

Rode o seguinte comando para criar as tabelas do banco de dados:
```
./vendor/bin/sail artisan migrate
```
Para inserir registros falsos rode o seguinte comando:
```
./vendor/bin/sail artisan db:seed --class=PatientTableSeeder
```
____

### Laravel Horizon

Use o Laravel Horizon para visualizar tarefas executando o seguinte comando:
```
./vendor/bin/sail artisan horizon
```
Acesse em [http://localhost/horizon]().
___

### Importação de arquivo .csv com dados de Pacientes

Para a importação do arquivo csv com dados de pacientes implementei uma regra para importar todos ou nenhum.

- Utilize como modelo o arquivo .csv localizado na pasta ``./docs/patients.csv``
- Para gerar CNS válido utilize https://geradornv.com.br/gerador-cns
- Para gerar CPF válido utilize https://www.geradordecpf.org

Caso algum registro não passe na validação de dados a importação será abortada.
Mensagens indicando os erros serão gravadas em um arquivo de log localizado em ``./storage/logs/import.log``.

___

### Tests

#### Rodando Tests

```
./vendor/bin/sail artisan test
```

#### Rodando Tests Coverage

```
./vendor/bin/sail artisan test --coverage
```

#### Gerar relatório de Test Coverage
```
./vendor/bin/sail phpunit --coverage-html coverage-report
```

Abra o arquivo ```./coverage-report/index.html``` com um navegador para visualizar o relatório.
