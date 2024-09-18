# 📋 Desafio Yii2

Bem-vindo ao projeto de desafio desenvolvido com Yii2! 🚀 Este projeto foi desenvolvido por Gabriel Vastella como uma exemplificação prática de um sistema em backend PHP.

## 📥 Começando

Siga estes passos para configurar e executar o projeto em seu ambiente local.

### 🔧 Pré-requisitos

Certifique-se de que você tem as seguintes ferramentas instaladas:

- **Docker e Docker Compose** (pode variar de `docker-compose` ou `docker compose`, de acordo com seu sistema operacional)
- **Makefile** (não é obrigatório, mas auxilia na execução)
- **Bucket AWS S3** (com permissões livres para criar itens públicos e sem ACL)

### ⚙️ Instalação

1. **Clone o repositório:**

    ```bash
    git clone https://github.com/gtvastella/desafio-yii2.git
    cd desafio-yii2
    ```

2. **Configure o arquivo de variáveis:**

    Renomeie o arquivo `.env.example` para `.env` e ajuste as seguintes variáveis:

    - **JWT_SECRET**: Chave secreta para autenticação JWT
    - **AWS_ACCESS_KEY_ID**: Chave ID do usuário IAM para o bucket da AWS
    - **AWS_BUCKET_NAME**: Nome do bucket S3
    - **MYSQL_ROOT_PASSWORD**: Senha do usuário root do MySQL (opcional, o padrão é `desafiopass`)
    - **MYSQL_DATABASE**: Nome do banco de dados MySQL (opcional, o padrão é `yii2`)

    O arquivo `.env` deve ter a seguinte estrutura:

    ```ini
    JWT_SECRET=your_jwt_secret
    AWS_ACCESS_KEY_ID=your_aws_access_key_id
    AWS_BUCKET_NAME=your_bucket_name
    MYSQL_ROOT_PASSWORD=your_mysql_root_password
    MYSQL_DATABASE=your_mysql_database
    ```

3. **Suba o container:**

    Utilize o auxiliar make para iniciar:

    ```bash
    make run
    ```
    OU (sem make)
    ```bash
    docker compose up -d
    ```

    Aguarde a construção da imagem do MySQL e PHP, instalação das dependências.

4. **Execute as migrações para criar as tabelas do banco de dados:**

    Na primeira inicialização, será necessário rodar migrações para criar a tabela dos bancos de dados:
    ```bash
    make migrate
    ```
    OU (sem make)
    ```bash
    docker exec -it yii2 php yii migrate --interactive=0
    ```
Pronto! A aplicação já está pronta e disponível para funcionar.

### 🚀 Executar/desligar a aplicação

Após a primeira inicialização, a aplicação já vai estar funcionando. Caso contrário, você pode iniciar usando o mesmo comando:

- **Ligar**
    ```bash
    make run
    ```
    OU (sem make)
    ```bash
    docker up -d
    ```

- **Desligar**
    
    ```bash
    make stop
    ```
    OU (sem make)
    ```bash
    docker compose down
    ```

### 🧑‍💻 Criar um Usuário 

- **Para criar um usuário, use o comando:**

    ```bash
    make create-user name=<nome> login=<login> password=<senha>
    ```
    OU (sem make)
    ```bash
    docker exec -it yii2 php yii user/create <nome> <login> <senha>
    ```

### 📚 Documentação

A documentação da API está disponível nos seguintes formatos:

- **Swagger YAML**: [doc.yaml](/doc.yaml)
- **Coleção Postman**: [collection.json](/collection.json)

### 💻 Outros comandos

- **Para ajuda e listagem de todos os comandos**

    ```bash
    make
    ```
   