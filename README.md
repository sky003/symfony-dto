# Just a sample of Symfony4 REST service

What's currently implemented here:
* User registration with email and password
* User verification via email
* User authentication with email and password
* User authentication with JWT
* Abstract CRUD controller with support of the versioned DTO objects (that's actually what this sample was created for)
* Sample domain to show how to implement a basic CRUD (domain called "interview")

## How it works

Here's a small BPMN diagram to show how it works:

![BPMN Diagram](/request-handler-bpmn.png?raw=true "BPMN Diagram")

## Running

Generate the SSH keys to sign JWT tokens with:
```
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

In case first openssl command forces you to input password use following to get the private key decrypted:
```
$ openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
$ mv config/jwt/private.pem config/jwt/private.pem-back
$ mv config/jwt/private2.pem config/jwt/private.pem
```

Run Docker based environment:
```
$ docker-compose up -d
```

## Tests

```
$ vendor/bin/codecept run
```

Right now only functional tests available here.