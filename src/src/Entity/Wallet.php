<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity()
*/
class Wallet
{
    /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
     * @ORM\Column(nullable=false, type="integer")
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency")
     */
    private $currency;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=false)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrency(): ?int
    {
        return $this->currency;
    }

    public function setCurrency(int $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
    
}
