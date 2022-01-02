# Locastic Blog API

## Description

An API for a demo blog application using Symfony 5 and ApiPlatform covered with PhpUnit tests. This project is a test for an interview by Locastic.

### Links

#### Heroku

[locasticblogapi](https://locasticblogapi.herokuapp.com)

#### GitHub

[apiblog](https://github.com/okmarq/apiblog)

### Installing locally

```bash
git clone https://github.com/okmarq/apiblog.git HTTPS
git clone git@github.com:okmarq/apiblog.git SSH
git clone gh repo clone okmarq/apiblog
GitHub CLI
```

rename ***.env.example*** to **.env** and configure `DATABASE_URL` and `MAILER_DSN` to suit your needs

```bash
composer install

npm install

php bin/console server:start

php bin/console doctrine:database:create

php bin/console make:migration

php bin/console doctrine:migrations:migrate

run migrations
```

#### User login by user role

##### Admin

Username: Admin
Password: admin

###### Admin permissions

can create read update and delete posts
can create read and update all requests

##### User

Username: User
Password: user

###### User permissions

can read posts only
can create read and update request

##### Blogger

Username: Blogger
Password: blogger

###### Blogger permissions

can create read update and delete posts
can read own request only

[^note]:
    styling sparsely or never used.
    functionality was my primary aim, however, I am capable of frontend development, using various libraries and frameworks. 
    tailwind is my most likely goto css library 