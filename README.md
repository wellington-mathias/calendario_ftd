# calendario_ftd
API para um calendário de eventos da FTD

Configuração:
  - Criar banco calendario_ftd no MySql
  - Executar script "sql/calendario_ftd.sql" para criar as tabelas
  - adicionar diretório "api" na raiz do projeto.

Obs.: Para acessar as operações da API é necessário enviar o HTTP Method correto de acordo com o padrão REST.
  - PUT para executar o CREATE
  - POST para UPDATE
  - GET para READ
  - DELETE para o DELETE
# Observações
     A Aplicação Deve Conter as seguintes Variaveis de Ambiente:

      - DB_HOST_CALENDARIOFTD=Host onde a base esta alocada
      - DB_USER_CALENDARIOFTD=calendario_ftd
      - DB_PASSWORD_CALENDARIOFTD=Senha de acesso
      - DB_DBNAME_CALENDARIOFTD=Usename de acesso
      - DB_PORT_CALENDARIOFTD=Porta da conexão

