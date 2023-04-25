# 4.0.2

* Updated `laminas/laminas-diactoros` to `2.25.2` due to a
  [security issue](https://github.com/advisories/GHSA-xv3h-4844-9h36).

# 4.0.1

* Fixed a bug where URLs generated in Twig templates, using the `route()` function, were absolute, i.e. always had a
  forward slash (`/`) in front of them; all routes generated this way will now always have their leading slash trimmed,
  except for the root route (`/`).

# 4.0.0

* Min. required version of PHP is now 8.1,
* [`Route`](https://github.com/Noctis/kickstart/blob/4.0.0/src/Http/Routing/Route.php) constructor methods now accept an
  optional 4th argument: name of custom request class,
* Custom HTTP requests are now passed to action's `process()` method as its `$request` argument,
* (Experimental) DIC builder functions are now vendor-agnostic,
* The following files are now includes in PHPCS & Psalm's inspections:
  * `bootstrap.php`,
  * `public/index.php`,
  * `bin/console`,
* Psalm inspection now includes looking for leftover calls to `dump()` function,
* Added a "switch" for enabling/disabling debugging settings & extended the list of PHP configuration options it changes 
  (see: [bootstrap.php](https://github.com/Noctis/kickstart-app/blob/4.0.0/bootstrap.php#L27)),
* Introduced [named routes](docs/Routing.md#named-routes) functionality,
* New method now available in Twig templates: `path()`, for generating URLs from route name,
* Removed `get()` method from HTTP request class & replaced it with two new methods: `fromQueryString()` & `fromBody()`,
* HTTP helper traits: `renderTrait()`, `redirectionTrait()` & `attachmentTrait()` now require different dependencies,
* Moved routes list from `src/Http/Routing/routes.php` to `config/routes.php`,
* Updated to Boostrap 5.2.3,
* Dropped jQuery from `templates/layout.html.twig`,
* Console commands can now be named _via_ class attribute (recommended),
* Updated MariaDB in Docker's `db` container to version 10.10,
* Latest version of [Composer](https://getcomposer.org/ is now available in Docker's PHP (`web`) container,
* Xdebug in Docker `web` container is now disabled by default (_opt-in_),
* Revised, updated & corrected docs,
* Updated root dependencies to their newest possible versions,
* Only [`paragonie/easydb`](https://github.com/paragonie/easydb) version 3.x is now supported; support for version 2.x
  has been dropped,
* Removed Composer's `psalm-ci` command in favor of specific command calls in CI config files,
* Updated Composer's `phpcs` script syntax so that the PHPCS config file (`.phpcs.xml.dist`) is no longer ignored by it.
