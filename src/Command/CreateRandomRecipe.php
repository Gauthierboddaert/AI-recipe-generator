<?php


namespace App\Command;

use App\Enum\DifficultyEnum;
use App\Service\Contract\GeneratorRecipeInterface;
use App\Service\RecipeManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'app:generate-random-recipe',
    description: 'Create a random recipe'
)]
class CreateRandomRecipe extends Command
{
    public function __construct(
        private readonly GeneratorRecipeInterface $generatorRecipe,
    )
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        for ($i = 0; $i < 100; $i++) {
            $io->title('Creating random recipe');
            $recipe = $this->generatorRecipe->generateRandomRecipe(DifficultyEnum::EASY);
            $io->success('Random recipe created : ' . $recipe->getName() . ', sleeping for 40 seconds...');
            sleep(40);
            $io->info('ready to create another random recipe!');
        }

        return Command::SUCCESS;
    }
}