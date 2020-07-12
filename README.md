# Projeto API Rest PHP
API para aplicativo que monitora a quantidade de vezes que o usuário bebeu água. O projeto foi desenvolvido como teste para uma vaga para Desenvolvedor Backend PHP.

## Tecnlogias/Componentes utilizados
- PHP 7.2
- [coffecode/datalayer](https://github.com/robsonvleite/datalayer "coffecode/datalayer") - componente para acesso a banco de dados;
- [coffecode/router](https://github.com/robsonvleite/router "coffecode/router") - componente para criação de rotas;
- [coffecode/paginator](https://github.com/robsonvleite/paginator "coffecode/paginator") - componente para paginação de resultados;
- [Composer](https://getcomposer.org/ "Composer") - gerenciador de dependências do PHP;
- **Banco de Dados:** MariaDB 10.4.11 - o dump da estrutura esta na pasta database.

## Endpoints da API
**POST** - **/users/** - criar um novo usuário
- **entrada**: email*, name*, password*

**POST** - **/login/** - autenticar usuário
- **entrada**: email*, password*
- **saída**: token, iduser, email, drink_couter

**PUT** - **/users/:userid** - editar seu próprio usuário
- **entrada**: email, name, password
- **header**: token*

**DELETE** - **/users/:userid** - deletar seu próprio usuário
- **header**: token*

**GET** - **/users/:iduser** - obter um usuário
- **saída**: iduser, name, drink_counter
- **header**: token*

**GET** - **/users/** - obter lista de usuários
- **saída**: (array de usuários)
- **header**: token*

**GET** - **/users/page/:page** - obter lista de usuários com paginação de resultados
- **saída**: (array de usuários)
- **header**: token*

**GET** - **/users/page/:page/limit/:limit** - obter lista de usuários com paginação de resultados e, limitando o número de registros por página
- **saída**: (array de usuários)
- **header**: token*

**POST** - **/users/:iduser/drink** - incrementar o contador de quantas vezes bebeu água
- **entrada**: drink_ml(int)
- **saída**: iduser, email, name, drink_counter
- **header**: token*

**PUT** - **/users/:iduser/drink/:drinkid** - atualizar contador de quantas vezes bebeu água
- **entrada**: drink_ml(int)
- **header**: token*

**DELETE** - **/users/:iduser/drink/:drinkid** - deletar registro de quantas vezes bebeu água
- **header**: token*

**GET** - **/users/:iduser/drinks** - lista histórico de registro do usuário
- **saída**: (array de drinks)
- **header**: token*

**GET** - **/users/:iduser/drinks/page/:page** - lista histórico de registro do usuário com paginação de resultados
- **saída**: (array de drinks)
- **header**: token*

**GET** - **/users/:iduser/drinks/page/:page/limit/:limit** - lista histórico de registro do usuário com paginação de resultados e limitando o número de registros por página
- **saída**: (array de drinks)
- **header**: token*

**GET** - **/users/:iduser/drinks/ranking** - lista ranking de usuários que beberam mais água no dia atual
- **saída**: (array de usuários)
- **header**: token*

**GET** - **/users/:iduser/drinks/ranking/page/:page** - lista ranking de usuários que beberam mais água no dia atual com paginação de resultados
- **saída**: (array de usuários)
- **header**: token*

**GET** - **/users/:iduser/drinks/ranking/page/:page/limit/:limit** - lista ranking de usuários que beberam mais água no dia atual com paginação de resultados e limitando o número de registros por página
- **saída**: (array de usuários)
- **header**: token*

*campos obrigatórios

## Restrições
- **Criação de usuários:** a API verifica se o email do usuário já está cadastrado e se o email informado esta em um formato válido. A senha precisa ter um tamanho de 8 a 40 caracteres (é possível editar o tamanho de caracteres para senha no arquivo Config.php). Antes de salvar a senha de usuário no banco de dados a API criptografa a senha usando a função password_hash;
- **Login:** a API informa caso o usuário ou senha estejam inválidos. Só são permitidas 3 tentativas de requisição de login em um período de 5 minutos;
- **Token de acesso:** o token de acesso tem a validade de 2 horas (é possível editar a duração em horas no arquivo Config.php);
- **Edição e remoção de usuários:** só é possível editar ou excluir seu próprio usuário, caso tente excluir ou editar outro usuário a API retorna uma mensagem uma de erro (“Sua requisição é inválida”);
- **Entradas e Saídas: **todas as entradas e saídas da API são no formato JSON.