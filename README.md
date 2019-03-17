Test task WarShip game
==================================

Simple PHP warship game

>For testing open and set in `env.php` 
```
$_ENV["TEST"]=false;
```

# Pre-installation #

Dependencies:

  * Docker engine v1.13 or higher. See [https://docs.docker.com/engine/installation](https://docs.docker.com/engine/installation)
  * Docker compose v1.12 or higher. See [docs.docker.com/compose/install](https://docs.docker.com/compose/install/)
 
# How to run #

Simply `cd` to project root folder and run `docker-compose up -d`. 
This will initialise and start all the containers, then leave them running in the background.
When need to run as `non demon` mode then just remove `-d` from command

## Open application on port 8080 ##

Webserver|[localhost:8080](http://localhost:8080)
