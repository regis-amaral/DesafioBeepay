## Desafio Beepay

Esta Api foi desenvolvida para o desafio de programação criado pela empresa Beepay. O objetivo do desafio consiste em desenvolver um cadastro de paciente, do qual possa ser testada a capacidade de criação de arquitetura, qualidade de código, validações e usabilidade.

Acesse a documentação de utilização no seguinte link:

https://documenter.getpostman.com/view/4127772/2sA2xh4DsU

## Requisitos 

Para instalar o projeto é necessário ter os seguintes aplicativos instalados e atualizados:
- Docker ^25.0.3
- Docker-compose ^2.24.6
- Composer ^2.7.1

## Instalando o projeto

Execute os seguintes passos:

1º Instalação de dependências
```
composer install
```

2 º Crie o arquivo .env com uma cópia do .env.example
```
cp .env.example .env
```

3º Construção da imagem dos containers

Use o seguinte comando para construir as imagem dos containers necessários para rodar o projeto:
```
./vendor/bin/sail build --no-cache
```
## Primeira execução

A primeira execução do projeto requer que sejam executados os seguintes passos:

1º - Subir os containers
```
./vendor/bin/sail up -d
```
2º - Gerar app key
```
./vendor/bin/sail artisan key:generate
```
3º - Rodar as migrations
```
./vendor/bin/sail artisan migrate
```
4º - Criar registros falsos no banco de dados
```
./vendor/bin/sail artisan db:seed --class=PatientTableSeeder
```

Tendo executado com sucesso os passos acima, abra o navegador e acesse o endereço http://localhost para verificar se a API está online.

Utilize a documentação do link abaixo para realizar as chamadas pré-configuradas com o Postman:

https://documenter.getpostman.com/view/4127772/2sA2xh4DsU

## Executar a aplicação

Para executar a aplicação use o comando:

```
./vendor/bin/sail up -d
```

## Importação de arquivo .csv com dados de Pacientes

Para a importação do arquivo csv com dados de pacientes o sistema obedece a regra de importar todos os dados com sucesso ou nenhum.
- Utilize como modelo o arquivo .csv localizado na pasta ``./docs/patients.csv``
- Para gerar CNS válido utilize https://geradornv.com.br/gerador-cns
- Para gerar CPF válido utilize https://www.geradordecpf.org

Caso algum registro não passe na validação de dados a importação será abortada.
No Horizon é possível ver o status das tarefas de importação.

Mensagens indicando os erros individuais de cada registro serão gravadas em um arquivo de log localizado em ``./storage/logs/import.log``.

## Laravel Horizon

A aplicação conta com o Laravel Horizon para visualizar as tarefas. 

Rode o comando abaixo para ativá-lo:
```
./vendor/bin/sail artisan horizon
```
Acesse em http://localhost/horizon


## Testes de Unidade e Funcionalidade

### Rodando Tests

```
./vendor/bin/sail artisan test
```

### Rodando Tests Coverage

```
./vendor/bin/sail artisan test --coverage
```

Resultado esperado:

![image](https://github.com/regis-amaral/DesafioBeepay/assets/118540708/28ccbf07-74f1-4d45-a659-7b624a927b3d)


### Gerar relatório de Test Coverage
```
./vendor/bin/sail phpunit --coverage-html coverage-report
```

Abra o arquivo ```./coverage-report/index.html``` com um navegador para visualizar o relatório.

## Comandos úteis:

#### Parar containers: 
```
./vendor/bin/sail stop
```
#### Acessar linha de comando: 
```
./vendor/bin/sail sheel
```
ou como root
```
./vendor/bin/sail root-shell
```
#### Logs em tempo real
```
./vendor/bin/sail root-shell -f
```
