# Development environment for PHP, MySQL and REDIS

## Useful commands

- `docker compose up -d` run the container in detached mode
- `docker compose down` close the container
- `docker ps` list of running container
- `docker service ps` list of running containers with service names
- `docker compose --build up` build the container and then runs up them
- `docker compose -f docker-compose.dev.yaml up -d` run the specified docker-compose file
- `docker exec -it php-docker-env-app-1 sh` enter inside container (opening shell) interactively
- `docker compose up --env-file .dev.env` specify which file for environment variable to use
  
Copy from vendor folder
- `$ cd /your/project/host/path`
- `$ docker cp $(docker-compose ps -q app):/var/www/html/vendor .`
  
### Development environemt
To run docker-compose.dev.yaml run:
`docker compose -f docker-compose.dev.yaml up --build -d`