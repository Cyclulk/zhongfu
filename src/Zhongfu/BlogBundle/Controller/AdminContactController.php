<?php

namespace Zhongfu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Zhongfu\BlogBundle\Entity\Contact;
use Zhongfu\BlogBundle\Form\ContactType;


class AdminContactController extends Controller
{

    /**
     * @Route("/admin/contact/add", name="_addContact")
     * @Template()
     */
    public function addContactAction(Request $request)
    {
        $oContact = new Contact();
        $oForm = $this->createForm(new ContactType(), $oContact);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            /**
             * @var $oContact Contact
             */
            if ($oForm->isValid()) {
                $oEntityManager = $this->getDoctrine()->getManager();
                $oEntityManager->persist($oContact);
                $oEntityManager->flush();

                unset($oContact);
                unset($oForm);
            }
            return $this->redirect($this->generateUrl('_contactList'));
        }

        return ['form' => $oForm->createView()];
    }

    /**
     * @Route("/admin/contact/modify/{id}",requirements = {"id":"[0-9]+"}, defaults={"id" = 0}, name="_contactModify")
     * @Template()
     */
    public function modifyContactAction(Request $request, $id)
    {

        $oEntityManager = $this->getDoctrine()->getManager();
        $oContactManager = $oEntityManager->getRepository("ZhongfuBlogBundle:Contact");
        $oContact = $oContactManager->findOneById($id);

        $oForm = $this->createForm(new ContactType(), $oContact);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            /**
             * @var $oContact Contact
             */
            if ($oForm->isValid()) {
                $oEntityManager = $this->getDoctrine()->getManager();
                $oEntityManager->persist($oContact);
                $oEntityManager->flush();

                unset($oContact);
                unset($oForm);
            }
            return $this->redirect($this->generateUrl('_contactList'));
        }

        return $this->render('ZhongfuBlogBundle:AdminContact:addContact.html.twig',array('form' => $oForm->createView()));
    }

    /**
     * @Route("/admin/contact/list",name="_contactList")
     * @Template()
     */
    public function listContactAction()
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $oContactManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Contact');
        $aContactList = $oContactManager->findAll();

        return ['contacts' => $aContactList];
    }

    /**
     * @Route("/admin/contact/delete/{id}",requirements = {"id":"[0-9]+"},name="_deleteContact")
     */
    public function deleteContactAction($id)
    {

        $oEntityManager = $this->getDoctrine()->getManager();
        $oContactManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Contact');

        $oContact = $oContactManager->findOneById($id);

        $oEntityManager->remove($oContact);
        $oEntityManager->flush();

        return $this->redirect($this->generateUrl('_contactList'));
    }
}