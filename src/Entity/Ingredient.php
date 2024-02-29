<?php

namespace App\Entity;

use App\Entity\Contract\EntityInterface;
use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Recipe::class, mappedBy: 'ingredients')]
    private Collection $recipes;

    #[ORM\ManyToMany(targetEntity: RecipeDetail::class, mappedBy: 'ingredients')]
    private Collection $recipeDetails;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->recipeDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): static
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
            $recipe->addIngredient($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): static
    {
        if ($this->recipes->removeElement($recipe)) {
            $recipe->removeIngredient($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, RecipeDetail>
     */
    public function getRecipeDetails(): Collection
    {
        return $this->recipeDetails;
    }

    public function addRecipeDetail(RecipeDetail $recipeDetail): static
    {
        if (!$this->recipeDetails->contains($recipeDetail)) {
            $this->recipeDetails->add($recipeDetail);
            $recipeDetail->addIngredient($this);
        }

        return $this;
    }

    public function removeRecipeDetail(RecipeDetail $recipeDetail): static
    {
        if ($this->recipeDetails->removeElement($recipeDetail)) {
            $recipeDetail->removeIngredient($this);
        }

        return $this;
    }
}
