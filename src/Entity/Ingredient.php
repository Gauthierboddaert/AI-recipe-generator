<?php

namespace App\Entity;

use App\Entity\Contract\EntityInterface;
use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recipe:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: RecipeDetail::class, mappedBy: 'ingredient')]
    private Collection $recipeDetails;

    public function __construct()
    {
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
            $recipeDetail->setIngredient($this);
        }

        return $this;
    }

    public function removeRecipeDetail(RecipeDetail $recipeDetail): static
    {
        if ($this->recipeDetails->removeElement($recipeDetail)) {
            // set the owning side to null (unless already changed)
            if ($recipeDetail->getIngredient() === $this) {
                $recipeDetail->setIngredient(null);
            }
        }

        return $this;
    }
}
