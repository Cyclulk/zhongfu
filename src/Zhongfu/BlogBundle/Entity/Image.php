<?php

namespace Zhongfu\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Zhongfu\BlogBundle\Entity\ImageRepository")
 */
class Image
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     * @Assert\Image(
     * mimeTypes = {"image/jpeg", "image/jpg","image/gif","image/png"},
     * mimeTypesMessage = "Choisissez une image valide")
     * @ORM\Column(name="file", type="string", length=255)
     */
    private $file;

    /**
     * @var string
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var Article
     * @ORM\ManyToOne(targetEntity="Article", cascade={"persist"})
     * @ORM\JoinColumn(name="article_id",referencedColumnName="id",onDelete="SET NULL")
     */
    private $article;

    /**
     * @var Evenement
     * @ORM\ManyToOne(targetEntity="Evenement", cascade={"persist"})
     * @ORM\JoinColumn(name="evenement_id",referencedColumnName="id",onDelete="SET NULL")
     */
    private $evenement;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get Evenement
     *
     * @return Evenement
     */
    public function getEvenement()
    {
        return $this->evenement;
    }

    /**
     * Set Evenement
     * @param Evenement $evenement
     *
     * @return Evenement
     */
    public function setEvenement($evenement)
    {
        $this->evenement = $evenement;
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
     * Set name
     *
     * @param string $name
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Image
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set article
     *
     * @param Article $article
     * @return Article
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get Article
     *
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    public function getUrl()
    {
        return $this->getPath().'/'. $this->getFile();
    }

    public function getMiniUrl()
    {
        return $this->getPath().'/mini_'. $this->getFile();
    }
}
