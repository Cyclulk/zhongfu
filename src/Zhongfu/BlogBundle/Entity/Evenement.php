<?php

namespace Zhongfu\BlogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Evenement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Zhongfu\BlogBundle\Entity\EvenementRepository")
 */
class Evenement
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
     * @ORM\Column(name="titre", type="string", length=150)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="avatar", referencedColumnName="id", nullable=true , onDelete="SET NULL")
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="date")
     */
    private $dateEnd;

    /**
     * @var Type
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="evenements")
     * @ORM\JoinColumn(name="type",referencedColumnName="id")
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity="FormSeminarFile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="form_seminar_file", referencedColumnName="id", nullable=true , onDelete="SET NULL")
     */
    private $formSeminarFile;

    public function addImagesList(Image $image)
    {
        $this->imageList[] = $image;
        $image->setEvenement($this);
        return $this;
    }

    public function avatarExist()
    {
        return $this->avatar ? true : false;
    }

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Image",mappedBy="evenement")
     */
    private $imagesList;

    /**
     * @var boolean
     * @ORM\Column(name="is_vedette", type="boolean", nullable=true)
     */
    private $is_vedette;

    function __construct()
    {
        $this->imageList = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getImagesList()
    {
        return $this->imagesList;
    }

    /**
     * @param Collection $imagesList
     */
    public function setImagesList($imagesList)
    {
        $this->imagesList = $imagesList;
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
     * Set titre
     *
     * @param string $titre
     * @return Evenement
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Evenement
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set avatar
     *
     * @param Image $image
     * @return Evenement
     */
    public function setAvatar($avatar)
    {

        $this->avatar = $avatar;
        return $this;
    }

    /**
     * Get avatar
     *
     * @return Image
     */
    public function getAvatar()
    {
        if ($this->avatar == null) {
            return $this->getImagesList()[0];
        }
        return $this->avatar;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Evenement
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Evenement
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTime $dateEnd
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }


    /**
     * Set type
     *
     * @param Type $type
     * @return Evenement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getFormSeminarFile()
    {
        return $this->formSeminarFile;
    }

    /**
     * @param mixed $formSeminarFile
     */
    public function setFormSeminarFile($formSeminarFile)
    {
        $this->formSeminarFile = $formSeminarFile;
    }

    /**
     * @return boolean
     */
    public function isIsVedette()
    {
        return $this->is_vedette;
    }

    /**
     * @param boolean $is_vedette
     */
    public function setIsVedette($is_vedette)
    {
        $this->is_vedette = $is_vedette;
    }

    public function isOneDay()
    {
        if ($this->getDate() >= $this->getDateEnd()){
            return true;
        }else{
            return false;
        }
    }

}
