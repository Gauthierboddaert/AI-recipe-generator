<?php

namespace App\Service\Importer;

use App\Enum\ImporterEnum;
use App\Factory\IngredientFactory;
use App\Service\Importer\Contract\ImporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IngredientImporter implements ImporterInterface
{
    public function __construct(
        private readonly string                 $importPath,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function import(): void
    {
        $ingredients = $this->getData();
        $this->handleDataIngredients($ingredients);

    }

    private function getData(): array
    {
        $spreadsheet = IOFactory::load($this->importPath . '/ingredient.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        $data = [];

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $data[$row->getRowIndex()][] = $cell->getValue();
            }
        }
        return $data;
    }

    private function handleDataIngredients(array $data): void
    {
        for ($i = 2; $i < 2850; $i++) {
            $ingredient = IngredientFactory::create($data[$i][0]);
            $this->entityManager->persist($ingredient);
        }

        $this->entityManager->flush();
    }

    public function supports(string $type): bool
    {
        return $type === ImporterEnum::INGREDIENT->value;
    }
}