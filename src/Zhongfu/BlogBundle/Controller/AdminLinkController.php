<?php

namespace Zhongfu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Zhongfu\BlogBundle\Entity\Contact;
use Zhongfu\BlogBundle\Entity\Link;
use Zhongfu\BlogBundle\Form\ContactType;
use Zhongfu\BlogBundle\Form\LinkType;


class AdminLinkController extends Controller
{

    /**
     * @Route("/admin/link/add", name="_addLink")
     * @Template()
     */
    public function addLinkAction(Request $request)
    {
        $oLink = new Link();
        $oForm = $this->createForm(new LinkType(), $oLink);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            /**
             * @var $oLink Link
             */
            if ($oForm->isValid()) {
                $oEntityManager = $this->getDoctrine()->getManager();
                $oEntityManager->persist($oLink);
                $oEntityManager->flush();

                unset($oLink);
                unset($oForm);
            }
            return $this->redirect($this->generateUrl('_linkList'));
        }

        return ['form' => $oForm->createView()];
    }

    /**
     * @Route("/admin/link/modify/{id}",requirements = {"id":"[0-9]+"}, defaults={"id" = 0}, name="_linkModify")
     * @Template()
     */
    public function modifyLinkAction(Request $request, $id)
    {

        $oEntityManager = $this->getDoctrine()->getManager();
        $oLinkManager = $oEntityManager->getRepository("ZhongfuBlogBundle:Link");
        $oLink = $oLinkManager->findOneById($id);

        $oForm = $this->createForm(new LinkType(), $oLink);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            /**
             * @var $oContact Contact
             */
            if ($oForm->isValid()) {
                $oEntityManager = $this->getDoctrine()->getManager();
                $oEntityManager->persist($oLink);
                $oEntityManager->flush();

                unset($oLink);
                unset($oForm);
            }
            return $this->redirect($this->generateUrl('_linkList'));
        }

        return $this->render('ZhongfuBlogBundle:AdminLink:addLink.html.twig',array('form' => $oForm->createView()));
    }

    /**
     * @Route("/admin/link/list",name="_linkList")
     * @Template()
     */
    public function listLinkAction()
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $oLinkManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Link');
        $aLinkList = $oLinkManager->findAll();

        return ['links' => $aLinkList];
    }

    /**
     * @Route("/admin/link/delete/{id}",requirements = {"id":"[0-9]+"},name="_deleteLink")
     */
    public function deleteLinkAction($id)
    {

        $oEntityManager = $this->getDoctrine()->getManager();
        $oLinkManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Link');

        $oLink = $oLinkManager->findOneById($id);

        $oEntityManager->remove($oLink);
        $oEntityManager->flush();

        return $this->redirect($this->generateUrl('_linkList'));
    }
}