<?php

namespace App\Entity;

use App\Entity\trait\TraitTimestampableEntity;
use App\Enum\StatusEnum;
use App\Repository\RecipeLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeLogRepository::class)]
class RecipeLog
{
    use TraitTimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Recipe $recipe = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $ingredientsPrompt = null;

    #[ORM\Column(type: Types::STRING, enumType: StatusEnum::class, options: ['default' => StatusEnum::NEW])]
    private StatusEnum $stateRecipe;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $ingredientsMissed = [];

    public function getStatusEnum(): StatusEnum
    {
        return $this->stateRecipe;
    }

    public function setStatusEnum(StatusEnum $statusEnum): self
    {
        $this->stateRecipe = $statusEnum;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getIngredientsPrompt(): ?string
    {
        return $this->ingredientsPrompt;
    }

    public function setIngredientsPrompt(string $ingredientsPrompt): static
    {
        $this->ingredientsPrompt = $ingredientsPrompt;

        return $this;
    }

    public function getIngredientsMissed(): array
    {
        return $this->ingredientsMissed;
    }

    public function setIngredientsMissed(array $ingredientsMissed): static
    {
        $this->ingredientsMissed = $ingredientsMissed;

        return $this;
    }
}
