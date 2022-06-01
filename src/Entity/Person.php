<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotIdenticalTo;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'delete', 'put'],
    attributes: [
        'pagination_enabled' => false,
    ],
)]
#[ApiFilter(
    SearchFilter::class, properties: [
        'firstname'=> 'partial',
        'lastname'=> 'partial',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'firstname' => SearchFilter::STRATEGY_PARTIAL,
        'lastname' => SearchFilter::STRATEGY_PARTIAL,
    ],
    arguments: ['orderParameterName' => 'order']
)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[NotBlank]

    #[Groups(['item'])]
    private string $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    #[NotBlank]

    #[Groups(['item'])]
    #[NotIdenticalTo(propertyPath: 'firstname')]
    private string $lastname;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
}
