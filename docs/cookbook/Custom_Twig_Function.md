# Registering a Custom Twig Function

Twig allows a developer to add/register their own custom functions. You can read more about it 
[here](https://twig.symfony.com/doc/3.x/advanced.html#functions). But, how can you do it in your Kickstart project? 
It's not like an instance of Twig is readily available.

You can register your custom Twig function through a DIC decorator. Here's how you can do it.

## Registering Through a DIC Decorator

To do this, you need to either edit one of your existing [service providers](../Service_Providers.md), or create a new
one. In the latter case, remember to register it in the `bin/console` and/or `public/index.php` files.

What you want to do, is to create a DIC decorator for the `Noctis\KickStart\Service\TemplateRendererInterface` 
interface.

Here's an example of registering a custom Twig function called `percentage()`, in a new service provider class:

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use Noctis\KickStart\Provider\ServicesProviderInterface;
use Noctis\KickStart\Service\TemplateRendererInterface;

use function DI\decorate;

final class ServicesProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            TemplateRendererInterface::class => decorate(
                function (TemplateRendererInterface $templateRenderer): TemplateRendererInterface {
                    $templateRenderer->registerFunction(
                        'percentage',
                        function (float $value, float $total): float {
                            if ($value >= $total) {
                                return 100;
                            } elseif ($value <= 0 || $total <= 0) {
                                return 0;
                            }

                            return ($value * 100) / $total;
                        }
                    );

                    return $templateRenderer;
                }
            ),
        ];
    }
}
```

Notice that we're using the `DI\decorate()` method here, which is part of the [PHP-DI](https://php-di.org/) package, 
Kickstart uses as its Dependency Injection Container. If you wish to know more about how this method can be used, you 
can read about it [here](https://php-di.org/doc/definition-overriding.html#decorators).

The `Noctis\KickStart\Service\TemplateRendererInterface::registerFunction()` is available in Kickstart 2.1.0 and up.
