<?php

namespace Zhongfu\BlogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article
 *Class that represent an article
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Zhongfu\BlogBundle\Entity\ArticleRepository")
 */
class Article
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
     * @Assert\NotBlank()
     * @ORM\Column(name="titre", type="string", length=150)
     */
    private $titre;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="slug", type="text")
     */
    private $slug;

    /**
     * @ORM\OneToOne(targetEntity="Image", mappedBy="article")
     * @ORM\JoinColumn(name="avatar", referencedColumnName="id", nullable=true , onDelete="SET NULL" )
     */
    private $avatar;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="date")
     */
    private $date;


    /**
     * @var Type
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="articles")
     * @ORM\JoinColumn(name="type",referencedColumnName="id")
     */
    private $type;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Image",mappedBy="article")
     */
    private $imagesList;

    /**
     * @var boolean
     * @ORM\Column(name="is_vedette", type="boolean", nullable=true)
     */
    private $is_vedette;

    function __construct(){
        $this->imageList = new ArrayCollection();
        $this->setDate(new \DateTime());
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

    public function addImagesList(Image $image){
        $this->imageList[] = $image;
        $image->setArticle($this);
        return $this;
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
     * @return Article
     */
    public function setTitre($titre)
    {
        $this->titre = strval($titre);

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
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = strval($slug);

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
     * @param integer $avatar
     * @return Image
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function avatarExist(){
       return  $this->avatar ? true :  false;
    }

    /**
     * Get avatar
     *
     * @return Image
     */
    public function getAvatar()
    {
        if  ($this->avatar == null){
            return $this->getImagesList()[0];
        }
        return $this->avatar;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Article
     */
    public function setContenu($contenu)
    {
        $this->contenu = strval($contenu);

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
     * @return Article
     */
    public function setDate($date)
    {
        if($date instanceof \DateTime){
        $this->date = $date;}

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
     * Set type
     *
     * @param Type $type
     * @return Article
     */
    public function setType($type)
    {
        $this->type =$type;

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

    public function IsNew()
    {
        if ($this->getTitre() == "" && $this->getContenu() == "" && $this->getSlug() == "" && $this->getType() == "" ){
            return true;
        }else{
            return false;
        }
    }
}
