# Running Your Kickstart App in Docker

Docker functionality has been introduced in Kickstart as early as 
[version 2.3.0](https://github.com/Noctis/kickstart-app/releases/tag/2.3.0) (in experimental form), but it wasn't until
[version 3.1.2](https://github.com/Noctis/kickstart-app/releases/tag/3.1.2) that it became actually stable.

[Docker](https://www.docker.com/) helps you develop your Kickstart-based application without having a copy of 
[PHP](https://www.php.net/), a web server (e.g. [Apache HTTP](https://httpd.apache.org/)) or a database server 
([MariaDB](https://mariadb.org/) or [MySQL](https://www.mysql.com/)) installed locally, i.e. on your computer, or on a 
remote machine.

In this document, I describe a couple of things you need to know if you decide to develop your Kickstart-based
application using Docker's containers.

## Kickstart's Default Docker Configuration

By default, Kickstart's application comes bundled with two Docker-related files:

* `docker-compose.yml`, and
* `Dockerfile`.

The `Dockerfile` contains information pertaining to the configuration of PHP & Apache's httpd (Web) server. Using this 
file you'll be able to launch a single container, with the latest PHP 8.0 & Apache 2.4 server available (w. PHP running 
in mod_php mode).

The `docker-compose.yml` file is more interesting, as it contains information regarding the MariaDB (database) server
and allows you to launch a set of two containers simultaneously: one with PHP & Apache, and one with MariaDB, both of 
which will be able to communicate with each other over Docker's internal network.

Those two containers are called:

* `web`, and
* `db`.

I think you can tell which one contains which services. To launch this container set, enter the root directory of your
Kickstart-based application in the command line (CLI) and execute the following command:

```shell
$ docker-compose up -d
```

If the Docker daemon is running and the `docker-compose` command is available, Docker should launch the two 
aforementioned containers. If the `docker-compose` command is not recognized, try running it like so:

```shell
$ docker compose up -d
```

If there were no problems building & running the two containers, then:

* Apache's httpd (Web) server will be available under [http://localhost:8008](http://localhost:8008),
* MariaDB server will be available under localhost:6603 (TCP).

Below you'll find a couple of mini-cookbook recipes, which will be helpful should you decide to develop your 
Kickstart-based application using Docker.

## Database Credentials in `.env`

If you want to connect to the single database offered by the MariaDB server running in the `db` container, you need to
modify your application's `.env` configuration file accordingly:

```dotenv
db_host=db
db_user=dbuser
db_pass=dbpass
db_name=dbname
db_port=3306
```

Why `db` as the hostname? Because that's the name of the container running the MariaDB server and also the network name
under which that container is visible from the `web` container, from which the PHP code will be run.

As for the username, password and database name - those three are defined in the `docker-compose.yml` file:

```yaml
db:
  image: mariadb:10.7-focal
  environment:
    MARIADB_RANDOM_ROOT_PASSWORD: yesplease
    MARIADB_USER: dbuser
    MARIADB_PASSWORD: dbpass
    MARIADB_DATABASE: dbname
  ports:
    - "6033:3306"
```

Why the 3306 port and not the 6033 one? 3306 is the port number under which the MariaDB server is available inside the
`db` container. The 6033 port is for you, if you wish to connect to this service from outside of Docker, i.e. from your
(host) computer.

MariaDB does have a `root` user, but since using it is discouraged due to security concerns, its password is generated
randomly every time you launch the `db` container. There is a way to discover what that password is, but I won't be
discussing this here.

## Populating the Database With Data on Start

When the `db` container is started, it only contains a single database (named `dbname` by default), which is empty. If
you wish to populate that database with some data, i.e. have some queries be run on it as the container is started, 
there is a way to do it.

By default, when this container is starting, it will look in its local `/docker-entrypoint-initdb.d` directory and
execute any files it finds there. So, if you have a directory with some .sql files you wish to run against the database, 
you should mount it to the `/docker-entrypoint-initdb.d` directory. To do it, you need to modify your application's
`docker-compose.yml` file and add a `volumes` section to it. For example, if your .sql files reside in the `db/scripts`
directory inside your application's root directory, the `volumes` section you add should look like so:

```yaml
db:
  image: mariadb:10.7-focal
  volumes:
    - ./db/scripts:/docker-entrypoint-initdb.d
```

When you run `docker-compose up` afterwards, your application's `db/scripts` directory will be mounted to 
`/docker-entrypoint-initdb.d` inside the `db` container and all the `.sql` files there will be executed against the 
`dbname` database, **in alphabetical order**.

If there are multiple files in a directory, but you only wish to execute one of them against the `dbname` database (for
example, only the `db/scripts/core.sql` file), you should modify your `docker-compose.yml` file like so:

```yaml
db:
  image: mariadb:10.7-focal
  volumes:
    - ./db/scripts/core.sql:/docker-entrypoint-initdb.d
```

**Remember that the contents of your MariaDB database running inside the `db` container will be deleted once the 
container is removed.** The data will not be lost if the container is stopped, without removing it.

## Running CLI Commands

If your Kickstart-based application has [console commands](Console.md), executable in CLI, running them will require
access to a PHP CLI interpreter, i.e. `php` executable. If you have PHP installed locally, on your computer, then 
running a console command is as simple as opening a CLI window, entering your application's root directory and executing
a command like so:

```shell
php bin/console dummy:command
```

In this scenario, if the command you're attempting to run does require a database connection, you will run into trouble,
if the database credentials in your applications `.env` file point to the `db` container, because from your computer's
point of view, there is no host called `db` in your network.

To connect to the MariaDB instance running in the `db` container you'd need to change the `db_host` to `localhost`, and
`db_port` to `6033`. Then it'll work. But now your application no longer works in the Web browser. Why is that? When you
run your application in a Web browser, the code is executed inside the `web` container, not on your local (host) 
computer. And from that container's point of view, the database credentials inside `.env` (`localhost:6603`) are 
invalid. So now your application runs fine in CLI, from your local computer, but won't run in your Web browser. 
Now what?

**If you decide to develop your application using Docker's containers, you need to use the `php` executable available in
`web` container, not your local one.**

There are two ways of doing it: you could tell Docker to execute a command inside the container and return the control
to you once it's done (the "one-off" method), or you could tell Docker to start a shell (terminal) session for you, 
inside the `web` container and let you have it, sort of like connecting to that container _via_ SSH.

### The "One-off" Method of Running Commands

If you just wish to execute a single command and return to your computer's CLI, execute the following command:

```shell
$ docker container exec -it kickstart-app-web-1 php bin/console
```

The `kickstart-app-web-1` name is the full name of the `web` container. This name changes depending on the name of the
directory in which your Kickstart application resides. To find out which container name you should use in your case,
look for it in the running containers list:

```shell
$ docker container ls
```

### Running Commands Inside a Container Terminal Session

If you plan on running more commands inside your `web` container, it would be better to launch a proper terminal session
inside the container. It'll be as if you're connected _via_ SSH, i.e. more familiar :)

To do it, execute the following command in your CLI:

```shell
$ docker container exec -it kickstart-app-web-1 /bin/bash
```

If you're not sure why the `kickstart-app-web-1` name here - look above. I've explained it in detail there.

Once you're in, you can run your commands as if you'd normally do:

```shell
$ php bin/console dummy:command
```

## XDebug

By default, I've designed the `Dockerfile` and `docker-compose.yml` files that ship with the Kickstart application to 
help you develop & debug your application, hence why I've enabled [Xdebug 3](https://xdebug.org/) by default.

For example, Xdebug's [remote debugging](https://xdebug.org/docs/step_debug) is enabled by default.

If there's anything you wish to change regarding Xdebug's configuration, you should modify the 
`docker/php/conf.d/xdebug.ini` file inside your Kickstart application's root directory. Any changes you make there will 
be visible the next time you run `docker-composer up -d`.

## Disabling Xdebug

As great and helpful Xdebug is, it does add a significant overhead to running your PHP code. If you wish to run your
application in Docker, but without Xdebug, edit the `docker/php/conf.d/xdebug.ini` file and comment out the 
`zend_extension=xdebug` line:

```ini
#zend_extension=xdebug

[xdebug]
xdebug.mode=develop,debug
xdebug.client_host=host.docker.internal
xdebug.start_with_request=yes
```

Once you start your `web` container, Xdebug will no longer be visible and everything should run much, much faster.

## Problems When Trying to Start the `web` Container

If you try and start the containers using `docker-compose up` and you see an error like this (example comes from a
machine running Windows 10):

```shell
kickstart-app-db-1   | 2022-03-31 19:11:19+00:00 [Note] [Entrypoint]: Initializing database files
Error response from daemon: failed to create shim: OCI runtime create failed: container_linux.go:380: starting container 
process caused: process_linux.go:545: container init caused: rootfs_linux.go:75: mounting 
"/run/desktop/mnt/host/c/Users/Kickstart/Desktop/projects/kickstart-app/docker/php/conf.d/xdebug.ini" to rootfs at 
"/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini" caused: mount through procfd: not a directory: unknown: Are you 
trying to mount a directory onto a file (or vice-versa)? Check if the specified host path exists and is the expected 
type
```

it means there was no `docker` folder inside your application's root directory. To remedy this, stop the containers by
running the following command:

```shell
$ docker-compose down
```

Remove the `docker` folder from inside your application's root directory (now it exists, but it's useless) and copy it
over from [Kickstart-app's GitHub repository](https://github.com/Noctis/kickstart-app).