<?php

namespace App\Command;

use App\Enum\ImporterEnum;
use App\Service\Importer\ImporterManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:import:ingredients', description: 'Import ingredients in database')]
class ImportIngredientsInDb extends Command
{
    public function __construct(
        private readonly ImporterManager $importerManager,
        ?string                          $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importing ingredients in database');
        $this->importerManager->import(ImporterEnum::INGREDIENT->value);
        return Command::SUCCESS;
    }
}