# Credit Card Manager

This API is responsible for management of credit cards.

## Install

This System uses Docker, so it is necessary Docker 
and Docker Compose installed to run this project, but you can configure nginx (or apache), php and mysql.

For install is necessary follow this steps:

**Install using Docker**

* Acess the directory of project
* give permissions for logs:
    * **MacOS:** `sudo chmod -R 777 storage`
    * **Linux:** `sudo chmod 777 -R storage`
* run this commands for install and start docker
    * `docker-compose build`
    * `docker-compose up -d`
* run this command to generate .env
    * `cp .env.example .env `   
* run this command to install libraries
    * docker run --rm --interactive --tty \     
         --volume $PWD:/app \         
         --user $(id -u):$(id -g) \         
         composer install
* run this command to generate key:
    * `docker-compose exec php php artisan key:generate`
* run this command for create tables
    * `docker-compose exec php php artisan migrate`
* run this command for create admin user and create initial data
    * `docker-compose exec php php artisan db:seed`
* run to generate private and public key of Oauth 2.0:
    * `docker-compose exec php php artisan passport:install`


**Install without Docker**
* Configure nginx (or apache), php and mysql;
* Acess the directory of project
* give permissions for logs:
    * `sudo chmod 777 -R storage`
* run this command to generate .env
    * `cp .env.example .env `   
* run this command to generate key:
    * `docker-compose exec php php artisan key:generate` 
* run this command to install libraries
    *  `composer install`
* run this command to create tables
    * `php artisan migrate`
* run this command to create admin user
    * `php artisan db:seed`
* run to generate private and public key of Oauth 2.0:
    * `docker-compose exec php php artisan passport:install`


## Tests
For run tests follow this steps in directory of project:
* run this command to run acceptance tests:    
    * `docker-compose exec php composer tests`

If you don't user Docker to Install use this commands in directory of project:
* run this command to run acceptance tests:    
    * `php composer tests`


## Usage
**IMPORTANT! All request with method POST need Content-Type in header**

**The Endpoints use Oauth 2.0, so to generate bearer token use the endpoint /v1/oauth/token with this body:**

`{
 	"client_id": 1, 	
 	"grant_type": "password",
 	"client_secret": "eB82RBLG47GT5RjVfOMaKhOBPyinWPFyuNUEuzYn",
 	"username": "admin@admin.com",
 	"password": "Admin123",
 	"scope": "admin"
 }`
 
**The token lasts 4 hours, in every request you need pass Token** 

Access this URL for docs of endpoints (this URL is only if you use Docker to install project):
 
 * **URL:** http://localhost:7080
 
Access this URL for API of endpoints ((this URL is only if you use Docker to install project):
  
  * **URL:** http://localhost:8080

_if you don't use Docker to install system you need configure the URL;_  

_if you don't use Docker to install, follow this steps to generate documentation:_

* _access this site https://editor.swagger.io/_
* _select File -> Import File
    * _select this file (directory of project)/docs/swagger.yaml__

