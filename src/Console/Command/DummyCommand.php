<?php declare(strict_types=1);
namespace App\Console\Command;

use Noctis\KickStart\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DummyCommand extends AbstractCommand
{
    protected static $defaultName = 'dummy:command';

    protected function configure(): void
    {
        $this
            ->setDescription('This is a dummy command, which does nothing.')
            ->setHelp(<<<EOH
You don't need no help, dummy!
EOH);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<comment>O hai!</comment>');

        return 0;
    }
}