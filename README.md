# EMAIL SENDER API

API to send email created in PHP.

## Getting started

### Prerequisite

Make sure these was installed:
* Docker
* Docker-compose

Then change `worker.env.example` in `worker` to `worker.env` and modify it to suits your configurations. Do the same thing with `server.env.example` in `server` folder.

### Running

* Run `docker-compose up --build` in console
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
    "message": "Email sent"
}
```