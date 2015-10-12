<?php

namespace Zhongfu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Zhongfu\BlogBundle\Entity\Poeme;
use Zhongfu\BlogBundle\Form\PoemeType;

class AdminPoemeController extends Controller
{

    /**
     * @Route("/admin/poeme/add", name="_addPoeme")
     * @Template()
     */
    public function addPoemeAction(Request $request)
    {
        $oPoeme = new Poeme();
        $oForm = $this->createForm(new PoemeType(), $oPoeme);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            /**
             * @var $oPoeme Poeme
             */
            if ($oForm->isValid()) {
                $oEntityManager = $this->getDoctrine()->getManager();
                $oEntityManager->persist($oPoeme);
                $oEntityManager->flush();

                unset($oPoeme);
                unset($oForm);
            }
            return $this->redirect($this->generateUrl('_poemeList'));
        }

        return ['form' => $oForm->createView()];
    }

    /**
     * @Route("/admin/poeme/modify/{id}",requirements = {"id":"[0-9]+"}, defaults={"id" = 0}, name="_poemeModify")
     * @Template()
     */
    public function modifyPoemeAction(Request $request, $id)
    {

        $oEntityManager = $this->getDoctrine()->getManager();
        $oPoemeManager = $oEntityManager->getRepository("ZhongfuBlogBundle:Poeme");
        $oPoeme = $oPoemeManager->findOneById($id);

        $oForm = $this->createForm(new PoemeType(), $oPoeme);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            /**
             * @var $oPoeme Poeme
             */
            if ($oForm->isValid()) {
                $oEntityManager = $this->getDoctrine()->getManager();
                $oEntityManager->persist($oPoeme);
                $oEntityManager->flush();

                unset($oPoeme);
                unset($oForm);
            }
            return $this->redirect($this->generateUrl('_poemeList'));
        }

        return $this->render('ZhongfuBlogBundle:AdminPoeme:addPoeme.html.twig',array('form' => $oForm->createView()));
    }

    /**
     * @Route("/admin/poeme/list",name="_poemeList")
     * @Template()
     */
    public function listPoemeAction()
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $oPoemeManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Poeme');
        $aPoemeList = $oPoemeManager->findAll();

        return ['poemes' => $aPoemeList];
    }

    /**
     * @Route("/admin/poeme/delete/{id}",requirements = {"id":"[0-9]+"},name="_deletePoeme")
     */
    public function deletePoemeAction($id)
    {

        $oEntityManager = $this->getDoctrine()->getManager();
        $oPoemeManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Poeme');

        $oPoeme = $oPoemeManager->findOneById($id);

        $oEntityManager->remove($oPoeme);
        $oEntityManager->flush();

        return $this->redirect($this->generateUrl('_poemeList'));
    }
}