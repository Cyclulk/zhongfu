<?php

namespace Zhongfu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Zhongfu\BlogBundle\Form\SendEmailContactType;
use Zhongfu\BlogBundle\Entity\SendEmailContact;

class IncludesController extends Controller
{
    /**
     * @Template()
     */
    public function navigationAction($oCategory = null)
    {
        $aTypes = $this->getDoctrine()->getManager()->getRepository('ZhongfuBlogBundle:Type')->findAll();
        $aLinks = $this->getDoctrine()->getManager()->getRepository('ZhongfuBlogBundle:Link')->findAll();

        if ($oCategory != null) {
            return ['category' => $oCategory, 'types' => $aTypes, 'links' => $aLinks];
        } else {
            return ['types' => $aTypes, 'links' => $aLinks];
        }
    }

    /**
     * @Template()
     */
    public function footerAction()
    {
        return [];
    }

    /**
     * @Template()
     */
    public function carouselAction()
    {
        return [];
    }

    /**
     * @Template()
     */
    public function relatedPostsAction($oPost)
    {
        $oEntityManager = $this->getDoctrine()->getManager();

        $query = $oEntityManager->createQuery('SELECT a FROM ZhongfuBlogBundle:Article a WHERE a.id != :id and a.type = :type')
            ->setParameter('id', $oPost->getId())
            ->setParameter('type', $oPost->getType());
        $aArticles = $query->getResult();

        $query = $oEntityManager->createQuery('SELECT a FROM ZhongfuBlogBundle:Evenement a WHERE a.id != :id and a.type != :type')
            ->setParameter('id', $oPost->getId())
            ->setParameter('type', $oPost->getType());
        $aEvenements = $query->getResult();


        return ['articles' => $aArticles, 'evenements' => $aEvenements];
    }

    /**
     * @Template()
     */
    public function enVedetteAction()
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $query = $oEntityManager->createQuery('SELECT a FROM ZhongfuBlogBundle:Article a WHERE a.is_vedette = TRUE ');
        $aArticles = $query->getResult();
        $query = $oEntityManager->createQuery('SELECT a FROM ZhongfuBlogBundle:Evenement a WHERE a.is_vedette = TRUE ');
        $aEvenements = $query->getResult();

        $aPosts = array_merge($aArticles, $aEvenements);

        return ['posts' => $aPosts];
    }

    /**
     * @Template()
     */
    public function contactAction()
    {
        $oSendEmailContact = new SendEmailContact();
        $oForm = $this->createForm(new SendEmailContactType(), $oSendEmailContact);


        $aContacts = $this->getDoctrine()->getManager()->getRepository('ZhongfuBlogBundle:Contact')->findAll();
        return ['contacts' => $aContacts, 'form' => $oForm->createView()];
    }

    /**
     * @Template()
     */
    public function coursAction()
    {
        return [];
    }

}