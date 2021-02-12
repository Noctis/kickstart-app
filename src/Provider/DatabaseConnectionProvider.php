<?php declare(strict_types=1);
namespace App\Provider;

use App\FancyConfigurationInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;
use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Exception\ConstructorFailed;
use ParagonIE\EasyDB\Factory;
use Psr\Container\ContainerInterface;

final class DatabaseConnectionProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            EasyDB::class => function (ContainerInterface $container): EasyDB {
                try {
                    $configuration = $container->get(FancyConfigurationInterface::class);

                    return Factory::fromArray([
                        sprintf(
                            'mysql:dbname=%s;host=%s;port=%s',
                            $configuration->getDbName(),
                            $configuration->getDbHost(),
                            $configuration->getDbPort()
                        ),
                        $configuration->getDbUser(),
                        $configuration->getDbPass()
                    ]);
                } catch (ConstructorFailed $ex) {
                    die('Could not connect to DB: '. $ex->getMessage());
                }
            },
        ];
    }
}