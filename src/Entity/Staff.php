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
     * @ORM\Column(type="text")
     */
    private $username;

    /**
     * @ORM\Column(type="text")
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
     * @ORM\Column(type="text")
     */
    private $qualification;

    /**
     * @ORM\Column(type="text")
     */
    private $organization;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalHoursPerYear;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalContractualHoursPerYear;

    /**
     * @ORM\Column(type="float")
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
     * @ORM\Column(type="text")
     */
    private $accountContactPerson;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accountIsNew;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $accountStartDate;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $accountEndDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accountProfile;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accountEmailEnabled;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accountWindowsEnabled;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accountLinuxEnabled;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $accountNote;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accountRequestDone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accountSipraDone;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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

    public function setQualification(string $qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getTotalHoursPerYear(): ?int
    {
        return $this->totalHoursPerYear;
    }

    public function setTotalHoursPerYear(int $totalHoursPerYear): self
    {
        $this->totalHoursPerYear = $totalHoursPerYear;

        return $this;
    }

    public function getTotalContractualHoursPerYear(): ?int
    {
        return $this->totalContractualHoursPerYear;
    }

    public function setTotalContractualHoursPerYear(int $totalContractualHoursPerYear): self
    {
        $this->totalContractualHoursPerYear = $totalContractualHoursPerYear;

        return $this;
    }

    public function getParttimePercent(): ?float
    {
        return $this->parttimePercent;
    }

    public function setParttimePercent(float $parttimePercent): self
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

    public function getAccountContactPerson(): ?string
    {
        return $this->accountContactPerson;
    }

    public function setAccountContactPerson(string $accountContactPerson): self
    {
        $this->accountContactPerson = $accountContactPerson;

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

    public function getAccountStartDate(): ?\DateTimeInterface
    {
        return $this->accountStartDate;
    }

    public function setAccountStartDate(\DateTimeInterface $accountStartDate): self
    {
        $this->accountStartDate = $accountStartDate;

        return $this;
    }

    public function getAccountEndDate(): ?\DateTimeInterface
    {
        return $this->accountEndDate;
    }

    public function setAccountEndDate(?\DateTimeInterface $accountEndDate): self
    {
        $this->accountEndDate = $accountEndDate;

        return $this;
    }

    public function getAccountProfile(): ?string
    {
        return $this->accountProfile;
    }

    public function setAccountProfile(string $accountProfile): self
    {
        $this->accountProfile = $accountProfile;

        return $this;
    }

    public function getAccountEmailEnabled(): ?bool
    {
        return $this->accountEmailEnabled;
    }

    public function setAccountEmailEnabled(bool $accountEmailEnabled): self
    {
        $this->accountEmailEnabled = $accountEmailEnabled;

        return $this;
    }

    public function getAccountWindowsEnabled(): ?bool
    {
        return $this->accountWindowsEnabled;
    }

    public function setAccountWindowsEnabled(bool $accountWindowsEnabled): self
    {
        $this->accountWindowsEnabled = $accountWindowsEnabled;

        return $this;
    }

    public function getAccountLinuxEnabled(): ?bool
    {
        return $this->accountLinuxEnabled;
    }

    public function setAccountLinuxEnabled(bool $accountLinuxEnabled): self
    {
        $this->accountLinuxEnabled = $accountLinuxEnabled;

        return $this;
    }

    public function getAccountNote(): ?string
    {
        return $this->accountNote;
    }

    public function setAccountNote(?string $accountNote): self
    {
        $this->accountNote = $accountNote;

        return $this;
    }

    public function getAccountRequestDone(): ?bool
    {
        return $this->accountRequestDone;
    }

    public function setAccountRequestDone(bool $accountRequestDone): self
    {
        $this->accountRequestDone = $accountRequestDone;

        return $this;
    }

    public function getAccountSipraDone(): ?bool
    {
        return $this->accountSipraDone;
    }

    public function setAccountSipraDone(bool $accountSipraDone): self
    {
        $this->accountSipraDone = $accountSipraDone;

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
}
