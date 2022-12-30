# FAQ

## Why are you using EasyDB and not something else (Doctrine, Eloquent, etc.)?

Two reasons:

* EasyDB is simple and lightweight, and makes PDO actually usable,
* it was created by the [Paragon Initiative Enterprises](https://paragonie.com/), which means it has an added layer of
  security on top of itself.
  
### OK, it's lightweight & secure, great, but writing more complex SQL queries "by hand" is a pain in the _you-know-what-!

Yes, it is. EasyDB ain't that great when you need to build your SQL query based on a number of variables, I agree. If
you need the flexibility that [Doctrine](https://www.doctrine-project.org/) or [Laravel's Eloquent](https://laravel.com/)
provide, I'd suggest you skip Kickstart and go for something like [Symfony](https://symfony.com/) or 
[Laravel](https://laravel.com/). Kickstart was designed with micro and small PHP projects in mind.

## Why is Kickstart [vendor-locked](https://en.wikipedia.org/wiki/Vendor_lock-in) to specific 3rd party components?

Kickstart uses Laminas' [components](https://docs.laminas.dev/components/) for HTTP functionality, Symfony's 
[components](https://symfony.com/doc/current/components/index.html) for Console functionality, 
[EasyDB](https://github.com/paragonie/easydb) for database connectivity, [PHP-DI](https://php-di.org/) for 
Dependency Injection, etc.

I could've created interfaces for those functionalities and vendor-specific providers (implementations) for them, 
allowing you to choose which vendor package would provide database connectivity, which vendor package would provide 
Dependency Injection, etc., kinda like what 
[Mezzio (formelly Zend Expressive)](https://docs.mezzio.dev/mezzio/v3/getting-started/quick-start/) does, but I didn't.
It would require me to perform research about possible functionality providers, which would then allow me to define the 
common interface and create the specific implementations for them. 

Problem is, I don't have the time for that. Kickstart was created out of necessity, as I create lots of micro and small 
projects in my line of work. I'm not trying to create yet another PHP framework here. I just created something which 
fits my needs just fine and decided to make it public. Maybe I'll revisit this idea in the future, but for now - 
nope, sorry.

## I keep getting 404 errors, no matter which URL I request.

Make sure the values:

* `basehref` in `.env` file, and
* `RewriteBase` in `public/.htaccess` file

are identical. If they are, make sure you're requesting the correct URL, which matches those values.
