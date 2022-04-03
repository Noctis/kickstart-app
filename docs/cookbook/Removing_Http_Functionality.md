# Removing the HTTP Functionality

Not all applications need to be available through the Web browser. If your application has no need for any HTTP
functionality, and you wish to prune any code related to it from your codebase, here's how you can do it.

Delete the following directories:

* `public`
* `src/Http`
* `templates`
* `var/cache/templates`

Remove the `src/Provider/HttpMiddlewareProvider.php` file. 

Edit the `bootstrap.php` file and remove `basehref` from the list of required configuration options 
(`$dotenv->required()`).

Edit the `.env` file and remove the following lines:

```dotenv
# "/" for root-dir, "/foo" (without trailing slash) for sub-dir
basehref=/
```

## Docker

If you wish to, you can also modify the Docker files that came with your application, removing any Web-based 
functionality.

First, remove `Dockerfile` and the `docker` directory from your application's root directory.

Next, modify the `docker-compose.yml` file, also in the application's root directory. Remove the entire definition of
the `web` service. In the end, this is what the file should look like:

```yaml
version: "3.9"

services:
  db:
    image: mariadb:10.7-focal
    environment:
      MARIADB_RANDOM_ROOT_PASSWORD: true
      MARIADB_USER: dbuser
      MARIADB_PASSWORD: dbpass
      MARIADB_DATABASE: dbname
    ports:
      - "6033:3306"
    networks:
      - kickstart-net

networks:
  kickstart-net: {}
```
