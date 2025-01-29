# staff
A service to store staff information and make it available via API


MIGRATION:
https://stackoverflow.com/questions/56317862/add-new-columns-to-a-existing-database-througt-doctrine-command-symfony
1 - change Entity file: bin/console make:entity
2 - bin/console make:migration
3 - bin/console doctrine:migrations:migrate

*** altra versione (non l'ho usata ...) ***
2 - php bin/console doctrine:migrations:diff
3 - php bin/console doctrine:migrations:migrate


NUOVI CAMPI PER DOTTORANDI/TIROCINANTI

    private $id;     /* @ORM\Column(type="integer")
    private $username;     /* @ORM\Column(type="text", nullable=true)
    private $email;     /* @ORM\Column(type="text", nullable=true)
    private $secondaryEmail;     /* @ORM\Column(type="text", nullable=true)
    private $name;     /* @ORM\Column(type="text")
    private $surname;     /* @ORM\Column(type="text")
    private $groupName;     /* @ORM\Column(type="text", nullable=true)
    private $leaderOfGroup;     /* @ORM\Column(type="text", nullable=true)
    private $qualification;     /* @ORM\Column(type="text", nullable=true)
    private $organization;     /* @ORM\Column(type="text", nullable=true)
    private $totalHoursPerYear;     /* @ORM\Column(type="integer", nullable=true)
    private $totalContractualHoursPerYear;     /* @ORM\Column(type="integer", nullable=true)
    private $parttimePercent;     /* @ORM\Column(type="float", nullable=true)
    private $isTimeSheetEnabled;     /* @ORM\Column(type="boolean")
    private $created;     /* @ORM\Column(type="datetimetz")
    private $validFrom;     /* @ORM\Column(type="datetimetz")
    private $validTo;     /* @ORM\Column(type="datetimetz")
    private $version;     /* @ORM\Column(type="text")
    private $note;     /* @ORM\Column(type="text", nullable=true)
    private $officePhone;     /* @ORM\Column(type="string", length=255, nullable=true)
    private $officeMobile;     /* @ORM\Column(type="string", length=255, nullable=true)
    private $officeLocation;     /* @ORM\Column(type="string", length=255, nullable=true)
    private $internalNote;     /* @ORM\Column(type="string", length=1024, nullable=true)
    private $lastChangeAuthor;     /* @ORM\Column(type="string", length=255)
    private $lastChangeDate;     /* @ORM\Column(type="datetimetz")
    private $descriptionList = [];     /* @ORM\Column(type="array", nullable=true)
    private $attachList = [];     /* @ORM\Column(type="array", nullable=true)

NUOVI CAMPI (tutti testo)

organizationOther
resultingThesisTitle
resultingAcquiredCompetences
tutorOfSchool
tutorOfFirm
corsoDiStudio
TitoloDiStudio
conseguitoA
conseguitoPresso
conseguitoInData
DepartmentOfOrigin
cityOfOrigin

ATTACH_LIST: progettoFormativo


