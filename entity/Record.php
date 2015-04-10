<?php

namespace App\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Record
 *
 * @ORM\Table(name="orc_records")
 * @ORM\Entity(repositoryClass="App\AppBundle\Entity\RecordRepository")
 */
class Record
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
     * @ORM\ManyToOne(targetEntity="App\AppBundle\Entity\Patient", inversedBy="records")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $patientId;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppBundle\Entity\Category", inversedBy="records")
     * @ORM\JoinColumn(name="type_record_id", referencedColumnName="id", nullable=false)
     */
    private $category;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppBundle\Entity\User", inversedBy="createdRecords")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
     */
    private $creatorId;

    /**
     * @ORM\Column(name="speciality", type="string", length=50, nullable=false)
     */
    private $speciality;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppBundle\Entity\User", inversedBy="signedRecords")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=true)
     */
    private $authorId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="signed_at", type="date", nullable=true)
     */
    private $signedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\AppBundle\Entity\Attachment", mappedBy="record")
     */
    private $attachments;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppBundle\Entity\Card", inversedBy="records")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $cardId;

    /**
     * @ORM\Column(name="last_editor", type="string", length=128, nullable=true)
     */
    private $lasteditor;

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
     * Set category
     *
     * @param \App\AppBundle\Entity\Category $category
     * @return Record
     */
    public function setCategory(\App\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \App\AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Record
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Record
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set signedAt
     *
     * @param \DateTime $signedAt
     * @return Record
     */
    public function setSignedAt($signedAt)
    {
        $this->signedAt = $signedAt;

        return $this;
    }

    /**
     * Get signedAt
     *
     * @return \DateTime 
     */
    public function getSignedAt()
    {
        return $this->signedAt;
    }

    /**
     * Set patientId
     *
     * @param \App\AppBundle\Entity\Patient $patientId
     * @return Record
     */
    public function setPatientId(\App\AppBundle\Entity\Patient $patientId)
    {
        $this->patientId = $patientId;

        return $this;
    }

    /**
     * Get patientId
     *
     * @return \App\AppBundle\Entity\Patient 
     */
    public function getPatientId()
    {
        return $this->patientId;
    }

    /**
     * Set creatorId
     *
     * @param \App\AppBundle\Entity\User $creatorId
     * @return Record
     */
    public function setCreatorId(\App\AppBundle\Entity\User $creatorId)
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    /**
     * Get creatorId
     *
     * @return \App\AppBundle\Entity\User 
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * Set authorId
     *
     * @param \App\AppBundle\Entity\User $authorId
     * @return Record
     */
    public function setAuthorId(\App\AppBundle\Entity\User $authorId = null)
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * Get authorId
     *
     * @return \App\AppBundle\Entity\User 
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add attachments
     *
     * @param \App\AppBundle\Entity\Attachment $attachments
     * @return Record
     */
    public function addAttachment(\App\AppBundle\Entity\Attachment $attachments)
    {
        $this->attachments[] = $attachments;

        return $this;
    }

    /**
     * Remove attachments
     *
     * @param \App\AppBundle\Entity\Attachment $attachments
     */
    public function removeAttachment(\App\AppBundle\Entity\Attachment $attachments)
    {
        $this->attachments->removeElement($attachments);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Set speciality
     *
     * @param string $speciality
     * @return Record
     */
    public function setSpeciality($speciality)
    {
        $this->speciality = $speciality;

        return $this;
    }

    /**
     * Get speciality
     *
     * @return string 
     */
    public function getSpeciality()
    {
        return $this->speciality;
    }

    /**
     * Set cardId
     *
     * @param \App\AppBundle\Entity\Card $cardId
     * @return Record
     */
    public function setCardId(\App\AppBundle\Entity\Card $cardId)
    {
        $this->cardId = $cardId;

        return $this;
    }

    /**
     * Get cardId
     *
     * @return \App\AppBundle\Entity\Card 
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * Set lasteditor
     *
     * @param string $lasteditor
     * @return Record
     */
    public function setLasteditor($lasteditor)
    {
        $this->lasteditor = $lasteditor;

        return $this;
    }

    /**
     * Get lasteditor
     *
     * @return string 
     */
    public function getLasteditor()
    {
        return $this->lasteditor;
    }
}
