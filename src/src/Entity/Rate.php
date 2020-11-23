<?php
namespace App\Entity;

use App\Component\ORMDate;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity()
*/
class Rate
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="ormdate",nullable=false)
     */
    private $date;

    /**
     * @ORM\Id()
     * @ORM\Column(nullable=false, type="integer")
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency")
     */
    private $currency;

    /**
    * @ORM\Column(type="decimal", precision=12, scale=2, nullable=false)
    */
    private $value;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date=$date;

        return $this;
    }

    public function setCurrency(int $currency): self
    {
        $this->currency=$currency;
        return $this;

    }

    public function getCurrency(): ?int
    {
        return $this->currency;
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
