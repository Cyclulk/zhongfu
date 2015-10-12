<?php

namespace Zhongfu\BlogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Type
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Zhongfu\BlogBundle\Entity\TypeRepository")
 */
class Type
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var Article
     * @ORM\OneToMany(targetEntity="Article",mappedBy="type")
     */
    private $articles;

    /**
     * @var Evenement
     * @ORM\OneToMany(targetEntity="Evenement",mappedBy="type")
     */
    private $evenements;

    /**
     * @return Article
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @param Article $articles
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;
    }

    /**
     * @return Evenement
     */
    public function getEvenements()
    {
        return $this->evenements;
    }

    /**
     * @param Evenement $evenements
     */
    public function setEvenements($evenements)
    {
        $this->evenements = $evenements;
    }

    /**
     * Type constructor.
     * @param Article $articles
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function addarticles(Article $article){
        $this->articles[] = $article;
        $article->setType($this);
        return $this;
    }

    public function addevenements(Evenement $evenement){
        $this->evenements[] = $evenement;
        $evenement->setType($this);
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
     * Set name
     *
     * @param string $name
     * @return Type
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
     * Set url
     *
     * @param string $url
     * @return Type
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }


}
