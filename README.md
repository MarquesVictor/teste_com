# Teste COMFICA

Este é um teste de desenvolvimento que consiste em criar um aplicativo web com um backend em Laravel, um frontend em React e um banco de dados MySQL.

## Instruções

As instruções detalhadas para o teste podem ser encontradas [neste link](https://dusty-sulfur-39d.notion.site/Teste-Desenvolvimento-aa843f2b9cee4d02a9fa754c8edaae33) em um documento do Notion.

## Backend

O backend do aplicativo está sendo desenvolvido em Laravel. Certifique-se de ter o Laravel instalado e configurado antes de prosseguir.

### Configuração do Laravel

1. Clone este repositório.
2. Acesse o diretório do backend usando o terminal:
cd backend

3. Instales as dependências do Laravel:
composer install

4. Crie um arquivo `.env` a partir do exemplo fornecido:
cp .env.example .env

5. Gere uma nova chave de aplicativo:
php artisan key:generate

### Banco de Dados

O banco de dados utilizado neste projeto é o MySQL. Certifique-se de ter o MySQL instalado e configurado corretamente.

### Migrações do Banco de Dados

1. Certifique-se de que as configurações do banco de dados no arquivo `.env` estejam corretas.
2. Rode as migrações para criar as tabelas do banco de dados:
php artisan migrate

## Frontend

O frontend do aplicativo está sendo desenvolvido em React. Certifique-se de ter o Node.js e o npm instalados antes de prosseguir.

## Configuração

1. Clone este repositório.
2. Siga as instruções fornecidas no link acima para configurar o frontend.
3. Verifique se você tem todas as dependências instaladas.

## Executando o Projeto

1. Inicie o servidor do Laravel:
cd backend
php artisan serve

2. Inicie o servidor do frontend (consulte as instruções fornecidas no link acima).
3. Abra o aplicativo no seu navegador.

## Contribuição

Sinta-se à vontade para contribuir com melhorias para este projeto. Basta fazer um fork deste repositório, fazer as alterações e enviar um pull request.

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).
