#Software Stack

DB - Mysql/Marian DB
Web Server - Apache configured with PHP fpm running Phalcon
Server Code - Phalcon Framework
Tech Stack - Docker and Docker Compose

##board index

this is the index refering to the board

0|1|2
-+-+-
3|4|5
-+-+-
6|7|8


##to run the stack

make sure you have docker and docker-compose

```bash
docker-compose up -d db
```

this will bring up the db container, and 

```bash
docker-compose up -d phalcon
```

which will bring up the apache/phalcon container and mount the codes inside


##to create a new game

```bash
curl -i localhost/api/v1/game -X POST

HTTP/1.1 200 OK
Date: Fri, 21 Apr 2017 18:13:31 GMT
Server: Apache/2.4.10 (Debian)
Content-Length: 48
Content-Type: text/html; charset=UTF-8

Game 2, O's turn 
 | | 
-+-+-
 | | 
-+-+-
 | | 
```

this will place O in the center of the board (position 3, type O)

```bash
curl -i localhost/api/v1/game/2 -X PUT -d '{"type":"O","where":4}'

HTTP/1.1 200 OK
Date: Fri, 21 Apr 2017 18:13:59 GMT
Server: Apache/2.4.10 (Debian)
Status: 200 OK
Content-Length: 48
Content-Type: text/html; charset=UTF-8

Game 2, X's turn 
 | | 
-+-+-
 |O| 
-+-+-
 | | 
```

the game will continue until all moves are exhusted


##extra notes

It was messy to put the controller inside the model in this case which I have implemented, it would be nicer to seperate game logic out to controller and have the model by it self handling ORM
