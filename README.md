# Atividade: Elaboração de aplicação web integrada a um banco de dados

Este repositório foi desenvolvido como resposta a atividade da semana 3 do módulo 7 da turma de Ciência da Computação do Inteli.

Há uma pasta [HMTL](https://github.com/Luiz-Granville/ponderadaAWS-Sem3/tree/69c17a368cd8b773f21b5d68985e4a475a9447f7/html)
 que possui dois arquivos PHP, nestas duas páginas existe a SamplePage.php que é um exemplo simples de criação e listagem de registros de com nome e endereço e o arquivo PersonManager.php foi desenvolvido como requisito da atividade, nela á um cadastramento de usuários com nome, idade e e-mail, a página possui um CRUD completo, lá é possivel cadastrar, visualizar, alterar e deletar usuários.

## SamplePage.php

### Conexão com o Banco de Dados: 
Este arquivo começa incluindo o arquivo "dbinfo.inc", que contém as informações de conexão com o banco de dados MySQL.

### Verificação da Tabela de Funcionários: 
A função VerifyEmployeesTable verifica se a tabela "EMPLOYEES" existe no banco de dados especificado. Se a tabela não existir, ela a cria com três colunas: ID (chave primária autoincremental), NAME (nome do funcionário) e ADDRESS (endereço do funcionário).

### Processamento do Formulário: 
O código verifica se os campos de entrada foram preenchidos. Se o nome ou o endereço tiverem sido preenchidos, o código usa a função AddEmployee para adicionar um novo funcionário à tabela "EMPLOYEES".

### Formulário de Entrada: 
O arquivo exibe um formulário onde os usuários podem inserir o nome e o endereço do novo funcionário.

### Exibição dos Dados da Tabela: 
O código exibe uma tabela que lista as informações de todos os funcionários na tabela "EMPLOYEES". Ele busca os dados do banco de dados usando uma consulta SELECT e itera sobre os resultados usando um loop while.

### Função AddEmployee: 
Esta função escapa os dados inseridos (para evitar injeção de SQL) e cria uma consulta SQL para inserir um novo funcionário na tabela "EMPLOYEES".

### Limpeza: 
No final do arquivo, o resultado da consulta é liberado (mysqli_free_result($result)) e a conexão com o banco de dados é fechada (mysqli_close($connection)).

## PersonManager.php 

### Conexão ao Banco de Dados:
O código começa com a inclusão do arquivo "dbinfo.inc", que contém as informações de conexão ao banco de dados (como servidor, nome de usuário, senha, etc.). Em seguida, ele estabelece a conexão com o banco de dados MySQL.

### Verificação da Tabela de Pessoas:
A função VerifyPersonsTable verifica se a tabela "Persons" existe no banco de dados especificado. Se a tabela não existir, ela a cria com três colunas: ID (chave primária autoincremental), Name (nome da pessoa), Age (idade da pessoa) e Email (email da pessoa).

### Processamento do Formulário:
O código verifica o método de requisição do servidor (GET ou POST) e a operação solicitada (criar, editar ou deletar). Dependendo da operação, ele executa as ações apropriadas.

### Criar: 
Se o formulário for enviado com uma operação "criar", o código pega os dados inseridos no formulário (Nome, Idade e Email) e os adiciona ao banco de dados usando a função AddPerson.

### Editar: 
Se a operação for "editar", o código obtém os dados atualizados do formulário e os utiliza para atualizar os dados da pessoa no banco de dados usando a função UpdatePerson.

### Deletar: 
Se a operação for "deletar", o código remove o registro da pessoa do banco de dados usando uma consulta SQL DELETE.

### Apresentação dos Dados:
O código exibe uma tabela que lista as informações de todas as pessoas na tabela "Persons". Ele busca os dados do banco de dados usando uma consulta SELECT e, em seguida, itera sobre os resultados usando um loop while. Cada pessoa é exibida em uma linha da tabela, juntamente com botões de edição e exclusão.

### Formulário de Edição:
Abaixo da tabela de pessoas, há um formulário de edição oculto. Quando o botão "Editar" em uma linha da tabela é clicado, o formulário de edição é preenchido com os detalhes da pessoa selecionada. O formulário de edição é exibido para permitir que o usuário faça alterações nos detalhes da pessoa.

### JavaScript Interativo:
O arquivo também contém código JavaScript que adiciona interatividade à página. Quando os botões "Editar" ou "Excluir" são clicados, o código JavaScript coleta os dados relevantes e executa a ação apropriada (mostrar o formulário de edição ou enviar o formulário de exclusão).

Estes arquivos foram instanciados na EC2 Linux com o RDS MySql, o banco de dados foi acessado com o uso das credenciais do banco de dados do arquivo dbinfo.inc.

O EC2 e o RDS foi configurado conforme o [tutorial](https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/TUT_WebAppWithRDS.html) disponibilizado na atividade ponderada.


Segue o [vídeo](https://drive.google.com/file/d/1u4CRzECCVKilYC5XvGe4aN6Cgh-vY71R/view?usp=sharing) de explicação da arquitetura.
