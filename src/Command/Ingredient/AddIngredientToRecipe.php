<?php

namespace App\Command\Ingredient;

use App\Enum\ImporterEnum;
use App\Service\Importer\ImporterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:add-ingredients:recipe', description: 'Import ingredients in database')]
class AddIngredientToRecipe extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        ?string                          $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Set ingredients to recipe');

        return Command::SUCCESS;
    }

}