Currency rate receiver
Project it is ready for use without configuration

First start of project:
Clone the repository

cd into project directory

start docker compose

#sudo docker compose up -d

install dependecies

#sudo docker compose exec php php -f composer.phar install

start script

#sudo docker compose exec php php -f leakprotect.php

second and later starts if docker compose runed, run only

#sudo docker compose exec php php -f leakprotect.php


For creation of DB dump into dbdumps dir

#sudo docker compose exec postgres sh -c "PGPASSWORD="admin" pg_dump -h localhost -U admin -s coin > /dbdumps/coin.sql"


Results of research of currency exchange services in exchangers file

