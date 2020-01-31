<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\StaffRepository")
 */
class Staff
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $secondaryEmail;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $surname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $groupName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $leaderOfGroup;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $qualification;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $organization;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalHoursPerYear;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalContractualHoursPerYear;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $parttimePercent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isTimeSheetEnabled;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $created;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $validFrom;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $validTo;

    /**
     * @ORM\Column(type="text")
     */
    private $version;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $officePhone;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $officeMobile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $officeLocation;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $internalNote;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastChangeAuthor;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $lastChangeDate;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $descriptionList = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $attachList = [];

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSecondaryEmail(): ?string
    {
        return $this->secondaryEmail;
    }

    public function setSecondaryEmail(?string $secondaryEmail): self
    {
        $this->secondaryEmail = $secondaryEmail;

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

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(?string $groupName): self
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getLeaderOfGroup(): ?string
    {
        return $this->leaderOfGroup;
    }

    public function setLeaderOfGroup(?string $leaderOfGroup): self
    {
        $this->leaderOfGroup = $leaderOfGroup;

        return $this;
    }

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(?string $qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(?string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getTotalHoursPerYear(): ?int
    {
        return $this->totalHoursPerYear;
    }

    public function setTotalHoursPerYear(?int $totalHoursPerYear): self
    {
        $this->totalHoursPerYear = $totalHoursPerYear;

        return $this;
    }

    public function getTotalContractualHoursPerYear(): ?int
    {
        return $this->totalContractualHoursPerYear;
    }

    public function setTotalContractualHoursPerYear(?int $totalContractualHoursPerYear): self
    {
        $this->totalContractualHoursPerYear = $totalContractualHoursPerYear;

        return $this;
    }

    public function getParttimePercent(): ?float
    {
        return $this->parttimePercent;
    }

    public function setParttimePercent(?float $parttimePercent): self
    {
        $this->parttimePercent = $parttimePercent;

        return $this;
    }

    public function getIsTimeSheetEnabled(): ?bool
    {
        return $this->isTimeSheetEnabled;
    }

    public function setIsTimeSheetEnabled(bool $isTimeSheetEnabled): self
    {
        $this->isTimeSheetEnabled = $isTimeSheetEnabled;

        return $this;
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

    public function setValidTo(\DateTimeInterface $validTo): self
    {
        $this->validTo = $validTo;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

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

    public function getOfficePhone(): ?string
    {
        return $this->officePhone;
    }

    public function setOfficePhone(?string $officePhone): self
    {
        $this->officePhone = $officePhone;

        return $this;
    }

    public function getOfficeMobile(): ?string
    {
        return $this->officeMobile;
    }

    public function setOfficeMobile(?string $officeMobile): self
    {
        $this->officeMobile = $officeMobile;

        return $this;
    }

    public function getOfficeLocation(): ?string
    {
        return $this->officeLocation;
    }

    public function setOfficeLocation(?string $officeLocation): self
    {
        $this->officeLocation = $officeLocation;

        return $this;
    }

    public function getInternalNote(): ?string
    {
        return $this->internalNote;
    }

    public function setInternalNote(?string $internalNote): self
    {
        $this->internalNote = $internalNote;

        return $this;
    }

    public function getLastChangeAuthor(): ?string
    {
        return $this->lastChangeAuthor;
    }

    public function setLastChangeAuthor(string $lastChangeAuthor): self
    {
        $this->lastChangeAuthor = $lastChangeAuthor;

        return $this;
    }

    public function getLastChangeDate(): ?\DateTimeInterface
    {
        return $this->lastChangeDate;
    }

    public function setLastChangeDate(\DateTimeInterface $lastChangeDate): self
    {
        $this->lastChangeDate = $lastChangeDate;

        return $this;
    }

    public function getDescriptionList(): ?array
    {
        return $this->descriptionList;
    }

    public function setDescriptionList(?array $descriptionList): self
    {
        $this->descriptionList = $descriptionList;

        return $this;
    }

    public function getAttachList(): ?array
    {
        return $this->attachList;
    }

    public function setAttachList(?array $attachList): self
    {
        $this->attachList = $attachList;

        return $this;
    }
}
