<?php

namespace App\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Patient
 *
 * @ORM\Table("orc_patients")
 * @ORM\Entity(repositoryClass="App\AppBundle\Entity\PatientRepository")
 */
class Patient
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=50)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="forename", type="string", length=50)
     */
    private $forename;

    /**
     * @var string
     *
     * @ORM\Column(name="patronymic", type="string", length=50, nullable=true)
     */
    private $patronymic;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date")
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var
     *
     * @ORM\Column(name="disability", type="string", length=100)
     */
    private $disability;

    /**
     * @var string
     *
     * @ORM\Column(name="edu", type="text", nullable=true)
     */
    private $edu;

    /**
     * @var string
     *
     * @ORM\Column(name="admission_diagnosis", type="text", nullable=true)
     */
    private $admissionDiagnosis;

    /**
     * @var string
     *
     * @ORM\Column(name="basic_diagnosis", type="text", nullable=true)
     */
    private $basicDiagnosis;

    /**
     * @var string
     *
     * @ORM\Column(name="con_diagnosis", type="text", nullable=true)
     */
    private $conDiagnosis;

    /**
     * @var string
     *
     * @ORM\Column(name="intolerant_drugs", type="text", nullable=true)
     */
    private $intolerantDrugs;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="about_family", type="text", nullable=true)
     */
    private $aboutFamily;

    /**
     * @ORM\OneToMany(targetEntity="App\AppBundle\Entity\Record", mappedBy="patientId")
     */
    protected $records;

    /**
     * @ORM\OneToMany(targetEntity="App\AppBundle\Entity\Card", mappedBy="patient")
     */
    protected $cards;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getSurname() . ' ' . $this->getForename();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return Patient
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set forename
     *
     * @param string $forename
     * @return Patient
     */
    public function setForename($forename)
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * Get forename
     *
     * @return string 
     */
    public function getForename()
    {
        return $this->forename;
    }

    /**
     * Set patronymic
     *
     * @param string $patronymic
     * @return Patient
     */
    public function setPatronymic($patronymic)
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    /**
     * Get patronymic
     *
     * @return string 
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return Patient
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }


    /**
     * Set disability
     *
     * @param string $disability
     * @return Patient
     */
    public function setDisability($disability)
    {
        $this->disability = $disability;

        return $this;
    }

    /**
     * Get disability
     *
     * @return string
     */
    public function getDisability()
    {
        return $this->disability;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Patient
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set edu
     *
     * @param string $edu
     * @return Patient
     */
    public function setEdu($edu)
    {
        $this->edu = $edu;

        return $this;
    }

    /**
     * Get edu
     *
     * @return string 
     */
    public function getEdu()
    {
        return $this->edu;
    }

    /**
     * Set basicDiagnosis
     *
     * @param string $basicDiagnosis
     * @return Patient
     */
    public function setBasicDiagnosis($basicDiagnosis)
    {
        $this->basicDiagnosis = $basicDiagnosis;

        return $this;
    }

    /**
     * Get basicDiagnosis
     *
     * @return string 
     */
    public function getBasicDiagnosis()
    {
        return $this->basicDiagnosis;
    }

    /**
     * Set conDiagnosis
     *
     * @param string $conDiagnosis
     * @return Patient
     */
    public function setConDiagnosis($conDiagnosis)
    {
        $this->conDiagnosis = $conDiagnosis;

        return $this;
    }

    /**
     * Get conDiagnosis
     *
     * @return string 
     */
    public function getConDiagnosis()
    {
        return $this->conDiagnosis;
    }

    /**
     * Set intolerantDrugs
     *
     * @param string $intolerantDrugs
     * @return Patient
     */
    public function setIntolerantDrugs($intolerantDrugs)
    {
        $this->intolerantDrugs = $intolerantDrugs;

        return $this;
    }

    /**
     * Get intolerantDrugs
     *
     * @return string 
     */
    public function getIntolerantDrugs()
    {
        return $this->intolerantDrugs;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Patient
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set aboutFamily
     *
     * @param string $aboutFamily
     * @return Patient
     */
    public function setAboutFamily($aboutFamily)
    {
        $this->aboutFamily = $aboutFamily;

        return $this;
    }

    /**
     * Get aboutFamily
     *
     * @return string 
     */
    public function getAboutFamily()
    {
        return $this->aboutFamily;
    }

    /**
     * Add records
     *
     * @param \App\AppBundle\Entity\Record $records
     * @return Patient
     */
    public function addRecord(\App\AppBundle\Entity\Record $records)
    {
        $this->records[] = $records;

        return $this;
    }

    /**
     * Remove records
     *
     * @param \App\AppBundle\Entity\Record $records
     */
    public function removeRecord(\App\AppBundle\Entity\Record $records)
    {
        $this->records->removeElement($records);
    }

    /**
     * Get records
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Set admissionDiagnosis
     *
     * @param string $admissionDiagnosis
     * @return Patient
     */
    public function setAdmissionDiagnosis($admissionDiagnosis)
    {
        $this->admissionDiagnosis = $admissionDiagnosis;

        return $this;
    }

    /**
     * Get admissionDiagnosis
     *
     * @return string 
     */
    public function getAdmissionDiagnosis()
    {
        return $this->admissionDiagnosis;
    }

    /**
     * Add card
     *
     * @param \App\AppBundle\Entity\Card $card
     * @return Patient
     */
    public function addCard(\App\AppBundle\Entity\Card $card)
    {
        $this->cards[] = $card;

        return $this;
    }

    /**
     * Remove card
     *
     * @param \App\AppBundle\Entity\Card $card
     */
    public function removeCard(\App\AppBundle\Entity\Card $card)
    {
        $this->cards->removeElement($card);
    }

    /**
     * Get card
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCards()
    {
        return $this->cards;
    }
}
