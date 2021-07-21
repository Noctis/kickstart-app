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
