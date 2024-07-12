# staff
A service to store staff information and make it available via API


E' stato previsot un comando per sincronizzare i dati di STAFF con l'agenda di area

Sync CARDS agenda

User not found

Nicolo' Ferron - in AREA e' chiamato: Nicolo' Alvise Ferron
Oisin Mc Cormack - in AREA e' chiamato: Oisin McCormack 
Alastair Shepherd - in AREA e' chiamato: Alastair Sheperd

Sono stati inseriti in area con nomi diversi

More than one user found:
Riccardo Agnello
Lucio Baseggio
Manola Carraro
Gianluca De Masi
Claudia Gasparrini
Paolo Scarin
Giuseppe Zollino

In area sono stati inseriti duplicati della stessa persona, ad esempio per Lucio Baseggio:
[
{"id":"33004","name":"Lucio","surname":"Baseggio","phone":"0498295091","phone2":"","fax":"0498700718"},
{"id":"60462","name":"Lucio","surname":"Baseggio","phone":"0498295091","phone2":"","fax":""}
]



LOG:
Sync CARDS agenda
CARDS: exportToAreaCards: URL=https://www.pd.cnr.it/services/rfx-api/rubrica/update.php?name=Domenico&surname=Abate&phone=5074 JSONRES={"result":"Successfully updated user #60152"}
CARDS: exportToAreaCards: URL=https://www.pd.cnr.it/services/rfx-api/rubrica/update.php?name=Riccardo&surname=Agnello&phone=5887 JSONRES={"result":"error","details":{"number":10,"message":"More than one user found (2)"}}
CARDS: exportToAreaCards: URL=https://www.pd.cnr.it/services/rfx-api/rubrica/update.php?name=Piero&surname=Agostinetti&phone=5087 JSONRES={"result":"Successfully updated user #33490"}
...
CARDS: exportToAreaCards: URL=https://www.pd.cnr.it/services/rfx-api/rubrica/update.php?name=Giuseppe&surname=Zollino&phone=5993&phone2=334.6053074 JSONRES={"result":"error","details":{"number":10,"message":"More than one user found (2)"}}
CARDS: exportToAreaCards: URL=https://www.pd.cnr.it/services/rfx-api/rubrica/update.php?name=Simone&surname=Zucchetti&phone=5027&phone2=331.6886333 JSONRES={"result":"Successfully updated user #33362"}
CARDS: exportToAreaCards: URL=https://www.pd.cnr.it/services/rfx-api/rubrica/update.php?name=Matteo&surname=Zuin&phone=5075 JSONRES={"result":"Successfully updated user #32157"}



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


