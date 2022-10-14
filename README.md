

## Installation
Currency rate receiver
Project it is ready for use without configuration

First start of project:
Clone the repository
```sh
#git clone https://github.com/dendroud/ratecollector.git
#cd ratecollector
```
Start docker compose:
```sh
#sudo docker compose up -d
```
Install dependecies:
```sh
#sudo docker compose exec php php -f composer.phar install
```
## Starting
Start script:
```sh
#sudo docker compose exec php php -f leakprotect.php
```

## Database

For creation of DB dump into dbdumps dir:
```sh
#sudo docker compose exec postgres sh -c "PGPASSWORD="admin" pg_dump -h localhost -U admin -s coin > /dbdumps/coin.sql"
```
Access to pgadmin: http://localhost:5050

## Other
Results of research of currency exchange services in "exchangers" file
