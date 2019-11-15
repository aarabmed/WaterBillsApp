<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WaterMeterRepository")
 * @ORM\HasLifecycleCallbacks
 */
class WaterMeter
{
    const NUM_ITEMS = 25;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Uuid
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="datetime")
     */
    private $setupDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = 1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="waterMeters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Address", mappedBy="waterMeter", cascade={"persist", "remove"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consumption", mappedBy="waterMeter")
     */
    private $consumptions;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSetupDate(): ?\DateTimeInterface
    {
        return $this->setupDate;
    }

    public function setSetupDate(\DateTimeInterface $setupDate): self
    {
        $this->setupDate = $setupDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active= $active ;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function __construct()
    {
        $this->code = Uuid::uuid4();
        $this->consumptions = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        // set (or unset) the owning side of the relation if necessary
        $newWaterMeter = $address === null ? null : $this;
        if ($newWaterMeter !== $address->getWaterMeter()) {
            $address->setWaterMeter($newWaterMeter);
        }

        return $this;
    }

    /**
     * @return Collection|Consumption[]
     */
    public function getConsumptions(): Collection
    {
        return $this->consumptions;
    }

    public function addConsumption(Consumption $consumption): self
    {
        if (!$this->consumptions->contains($consumption)) {
            $this->consumptions[] = $consumption;
            $consumption->setWaterMeter($this);
        }

        return $this;
    }

    public function removeConsumption(Consumption $consumption): self
    {
        if ($this->consumptions->contains($consumption)) {
            $this->consumptions->removeElement($consumption);
            // set the owning side to null (unless already changed)
            if ($consumption->getWaterMeter() === $this) {
                $consumption->setWaterMeter(null);
            }
        }

        return $this;
    }
}
