# 4.0.0

* Min. required version of PHP is now 8.1,
* Custom HTTP requests are now passed to action's `process()` method as its `$request` argument,
* (Experimental) DIC builder functions are now vendor-agnostic,
* `bootstrap.php` is now included in PHPCS inspection,
* `public/index.php` & `bin/console` are now included in Psalm inspection,
* Psalm inspection now includes looking for leftover calls to `dump()` function,
* Added a "switch" for enabling/disabling debugging settings & extended the list of PHP configuration options it changes,
* Introduced [named routes](docs/Routing.md#named-routes) functionality,
* Removed `get()` method from HTTP request class & replaced it with two new methods: `fromQueryString()` & `fromBody()`,
* HTTP helper traits: `renderTrait()`, `redirectionTrait()` & `attachmentTrait()` now require different dependencies,
* Moved routes list from `src/Http/Routing/routes.php` to `config/routes.php`,
* Updated to Boostrap 5.2.3,
* Console commands can now be named _via_ class attribute (recommended),
* Updated MariaDB in Docker container to version 10.10,
* Xdebug in Docker container is now disabled by default (_opt-in_),
* Revised, updated & corrected docs,
* Updated root dependencies to their newest possible versions.
