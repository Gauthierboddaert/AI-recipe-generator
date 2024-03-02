<?php

namespace App\Entity;

use App\Entity\Contract\EntityInterface;
use App\Entity\trait\TraitTimestampableEntity;
use App\Enum\StatusEnum;
use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe implements EntityInterface
{
    use TraitTimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recipe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recipe:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, length: 1000)]
    #[Groups(['recipe:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 1])]
    #[Groups(['recipe:read'])]
    private ?int $numberPerson;

    #[ORM\Column(type: Types::STRING, enumType: StatusEnum::class, options: ['default' => StatusEnum::NEW])]
    #[Groups(['recipe:read'])]
    private StatusEnum $stateIngredientIsSet;

    #[ORM\ManyToMany(targetEntity: RecipeDetail::class, mappedBy: 'recipe')]
    #[Groups(['recipe:read'])]
    private Collection $recipeDetails;

    public function __construct()
    {
        $this->recipeDetails = new ArrayCollection();
    }

    public function getStatusEnum(): StatusEnum
    {
        return $this->stateIngredientIsSet;
    }

    public function setStatusEnum(StatusEnum $statusEnum): self
    {
        $this->stateIngredientIsSet = $statusEnum;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
            $recipeDetail->addRecipe($this);
        }

        return $this;
    }

    public function removeRecipeDetail(RecipeDetail $recipeDetail): static
    {
        if ($this->recipeDetails->removeElement($recipeDetail)) {
            $recipeDetail->removeRecipe($this);
        }

        return $this;
    }

    public function getStateIngredientIsSet(): StatusEnum
    {
        return $this->stateIngredientIsSet;
    }

    public function setStateIngredientIsSet(StatusEnum $stateIngredientIsSet): void
    {
        $this->stateIngredientIsSet = $stateIngredientIsSet;
    }

    public function getNumberPerson(): ?int
    {
        return $this->numberPerson;
    }

    public function setNumberPerson(?int $numberPerson): self
    {
        $this->numberPerson = $numberPerson;

        return $this;
    }
}
