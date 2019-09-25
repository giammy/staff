<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $requested;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $surname;

    /**
     * @ORM\Column(type="text")
     */
    private $contactPerson;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accountIsNew;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $validFrom;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $validTo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $groupName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $emailEnabled;

    /**
     * @ORM\Column(type="boolean")
     */
    private $windowsEnabled;

    /**
     * @ORM\Column(type="boolean")
     */
    private $linuxEnabled;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(?\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getRequested(): ?\DateTimeInterface
    {
        return $this->requested;
    }

    public function setRequested(\DateTimeInterface $requested): self
    {
        $this->requested = $requested;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function setContactPerson(string $contactPerson): self
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    public function getAccountIsNew(): ?bool
    {
        return $this->accountIsNew;
    }

    public function setAccountIsNew(bool $accountIsNew): self
    {
        $this->accountIsNew = $accountIsNew;

        return $this;
    }

    public function getValidFrom(): ?\DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTimeInterface $validFrom): self
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(?\DateTimeInterface $validTo): self
    {
        $this->validTo = $validTo;

        return $this;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(?string $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(?string $groupName): self
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getEmailEnabled(): ?bool
    {
        return $this->emailEnabled;
    }

    public function setEmailEnabled(bool $emailEnabled): self
    {
        $this->emailEnabled = $emailEnabled;

        return $this;
    }

    public function getWindowsEnabled(): ?bool
    {
        return $this->windowsEnabled;
    }

    public function setWindowsEnabled(bool $windowsEnabled): self
    {
        $this->windowsEnabled = $windowsEnabled;

        return $this;
    }

    public function getLinuxEnabled(): ?bool
    {
        return $this->linuxEnabled;
    }

    public function setLinuxEnabled(bool $linuxEnabled): self
    {
        $this->linuxEnabled = $linuxEnabled;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
