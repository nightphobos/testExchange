<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="transaction",indexes={
 *     @ORM\Index(name="to_indx", columns={"to"}),
 *     @ORM\Index(name="from_indx", columns={"from"})
 * })
 */
class Transaction
{
    /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created;

    /**
    * @ORM\Column(type="decimal", precision=12, scale=2, nullable=false, name="`value`")
    */
    private $value;

    /**
     * @ORM\Column(nullable=true, type="integer", name="`from`")
     * @ORM\OneToMany (targetEntity="App\Entity\Wallet", mappedBy="id")
     */
    private $from;

    /**
     * @ORM\Column(nullable=false, type="integer", name="`to`")
     * @ORM\OneToMany (targetEntity="App\Entity\Wallet", mappedBy="id")
     */
    private $to;

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = new \DateTime("now");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

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

    public function getFrom(): ?int
    {
        return $this->from;
    }

    public function setFrom($from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?int
    {
        return $this->to;
    }

    public function setTo($to): self
    {
        $this->to = $to;

        return $this;
    }

    
}
