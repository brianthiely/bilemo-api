# Bilemo API

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/ffa7c285810d45bd9dd9fc241c05bafc)](https://app.codacy.com/gh/brianthiely/bilemo-api/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Description

This API is a REST API that allows you to manage a catalog of mobile phones and their users.

## Installation

1. Clone the repository
2. Install the dependencies with composer
3. Create the database and configure the .env file
4. Create the JWT keys
5. Load the fixtures
6. Run the server

### 1. Clone the repository

```bash 
git clone
``` 

### 2. Install the dependencies with composer

```bash
composer install
```

### 3. Create the database and configure the .env file

```bash
php bin/console doctrine:database:create
```

### 4. Create the JWT keys

```bash
mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

### 5. Load the fixtures

```bash
php bin/console doctrine:fixtures:load
```

### 6. Run the server

```bash
symfony server:start
```

## Documentation

The documentation is available at the following address: [https://127.0.0.1:8000/api/doc](https://127.0.0.1:8000/api/doc)


