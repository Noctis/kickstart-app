# Registering a Twig Extension

Twig allows a developer to add/register additional extensions. You can read more about it
[here](https://twig.symfony.com/doc/3.x/advanced.html#creating-an-extension). How can you register a Twig extension in 
your Kickstart project?

You can register a Twig extension through a DIC decorator. Here's how you can do it.

## Registering Through a DIC Decorator

To do this, you need to either edit one of your existing [service providers](../Service_Providers.md), or create a new
one. In the latter case, remember to register it in the `src\Http\Application.php` (and/or 
`src/Console/Application.php`) file.

What you want to do, is to create a DIC decorator for the `Noctis\KickStart\Service\TemplateRendererInterface`
interface.

Here's an example of registering Symfony's [translation extension](https://github.com/symfony/translation-contracts) in 
Twig:  

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use Noctis\KickStart\Provider\ServicesProviderInterface;
use Noctis\KickStart\Service\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extra\Intl\IntlExtension;

use function Noctis\KickStart\Service\Container\decorator;

final class TwigExtensionsProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            TemplateRendererInterface::class => decorator(
                function (
                    TemplateRendererInterface $templateRenderer,
                    ContainerInterface $container
                ): TemplateRendererInterface {
                    $templateRenderer->registerExtension(
                        new TranslationExtension(
                            $container->get(TranslatorInterface::class)
                        )
                    );
                    $templateRenderer->registerExtension(
                        new IntlExtension()
                    );

                    return $templateRenderer;
                }
            ),
        ];
    }
}
```

**NOTICE:** The `Noctis\KickStart\Service\TemplateRendererInterface::registerFunction()` is available in Kickstart
2.1.0 and up.
