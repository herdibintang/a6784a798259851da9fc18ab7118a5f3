# EMAIL SENDER API

API to send email created in PHP.

## Getting started

### Prerequisite

Make sure these was installed:
* PHP
* Composer
* Postgresql
* RabbitMQ

Then create a `.env` file that contains:
```
MAIL_HOST=smtp.example.com
MAIL_PORT=12345
MAIL_USERNAME=username
MAIL_PASSWORD=password

DB_HOST=0.0.0.0
DB_USERNAME=username
DB_PASSWORD=password
DB_DATABASE=email_sender

RABBITMQ_HOST=0.0.0.0
RABBITMQ_PORT=12345
RABBITMQ_USERNAME=username
RABBITMQ_PASSWORD=password
```
Change it to suits your configurations.

### Installing

Run `composer install`

### Running

* Run `php worker.php` in console
* Run `php -S localhost:8000 server.php` in another console
* API now accessible in `http://localhost:8000`

## Usages

To use first you must create a user, get a token, set the token to Authorization header, then send request to send email route.

### Create User

Url : /user
Method : POST
Example Request :
```
{
	"email": "a@b.com",
	"password": "password"
}
```
Example Response :
```
{
    "message": "User created"
}
```

### Get Token / Login

Url : /user/login
Method : POST
Example Request :
```
{
	"email": "a@b.com",
	"password": "password"
}
```
Example Response :
```
{
    "message": "Token created",
    "token": "sdjklasdmklasdnasdj"
}
```

### Send Email

Url : /email/send
Method : POST
Example Request :
```
{
	"from": "a@b.com",
	"to": "c@d.com",
	"subject": "subject",
	"body": "body"
}
```
Example Response :
```
{
    "message": "Email request added to queue"
}
```