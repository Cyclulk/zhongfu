<?php

namespace Zhongfu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Zhongfu\BlogBundle\Entity\Evenement;
use Zhongfu\BlogBundle\Entity\FormSeminarFile;
use Zhongfu\BlogBundle\Entity\Image;
use Zhongfu\BlogBundle\Form\EvenementType;
use Zhongfu\BlogBundle\Form\FormSeminarFileType;


class AdminEvenementController extends Controller
{

    /**
     * @Route("/admin/evenement/add",name="_addEvenement")
     * @Template()
     */
    public function addEvenementAction(Request $request)
    {
        $oEvenement = new Evenement();
        $oForm = $this->createForm(new EvenementType(), $oEvenement);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            /**
             * @var $oEvenement Evenement
             */
            if ($oForm->isValid()) {
                $oEntityManager = $this->getDoctrine()->getManager();
                $oEntityManager->persist($oEvenement);
                $oEntityManager->flush();

                $id = $oEvenement->getId();

                unset($oEvenement);
                unset($oForm);
                return $this->redirect($this->generateUrl('_postsImageModifier', array('id' => $id, 'postType' => "evenement")));
            }
        }

        return ['form' => $oForm->createView()];
    }

    /**
     * @Route("/admin/evenement/modify/{id}",requirements = {"id":"[0-9]+"},name="_evenementModify")
     * @Template()
     */
    public function modifyEvenementAction(Request $request, $id)
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $oEvenementManager = $oEntityManager->getRepository("ZhongfuBlogBundle:Evenement");

        $oEvenement = $oEvenementManager->findOneById($id);
        $oForm = $this->createForm(new EvenementType(), $oEvenement);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            /**
             * @var $oEvenement Evenement
             */
            if ($oForm->isValid()) {
                $oEntityManager->persist($oEvenement);
                $oEntityManager->flush();

                unset($oEvenement);
                unset($oForm);

                return $this->redirect($this->generateUrl('_homepage'));
            }
        }
        return ['form' => $oForm->createView()];
    }

    /**
     * @Route("/admin/evenement/list",name="_evenementList")
     * @Template()
     */
    public function listEvenementAction()
    {

        $oEntityManager = $this->getDoctrine()->getManager();
        $oEvenementManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Evenement');
        $aEvenementsList = $oEvenementManager->findBy(array(), array('date' => 'desc'));

        return ['evenements' => $aEvenementsList];
    }

    /**
     * @Route("/admin/evenement/delete/{id}",requirements = {"id":"[0-9]+"},name="_deleteEvenement")
     */
    public function deleteEvenementAction($id)
    {

        $oEntityManager = $this->getDoctrine()->getManager();
        $oEvenementManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Evenement');

        $oEvenement = $oEvenementManager->findOneById($id);

        //We delete all the image related to the article
        /**
         * @var $oEvenement Evenement
         */
        if ($oEvenement->getAvatar()) {
            foreach ($oEvenement->getImagesList() as $oImage) {

                if (file_exists($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/' . $oImage->getFile())) {
                    unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/' . $oImage->getFile());
                }
                if (file_exists($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile())) {
                    unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile());
                }
                $oEntityManager->remove($oImage);
            }
            //and the avatar
            if (file_exists($this->container->getParameter('upload_base_dir') . $oEvenement->getAvatar()->getPath() . '/' . $oImage->getFile())) {
                unlink($this->container->getParameter('upload_base_dir') . $oEvenement->getAvatar()->getPath() . '/' . $oImage->getFile());
            }
            if (file_exists($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile())) {
                unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile());
            }

            rmdir($this->container->getParameter('upload_base_dir') . $oImage->getPath());
            $oEntityManager->remove($oImage);
        }

        //delete the pdf
        if ($oEvenement->getFormSeminarFile()) {
            $oPdf = $oEvenement->getFormSeminarFile();

            if (file_exists($this->container->getParameter('upload_base_dir') . $oPdf->getPath() . '/' . $oPdf->getFile())) {
                unlink($this->container->getParameter('upload_base_dir') . $oPdf->getPath() . '/' . $oPdf->getFile());
            }
            rmdir($this->container->getParameter('upload_base_dir') . $oPdf->getPath());
            $oEntityManager->remove($oPdf);
        }

        $oEntityManager->remove($oEvenement);
        $oEntityManager->flush();

        return $this->redirect($this->generateUrl('_evenementList'));
    }

    /**
     * @Route("/admin/evenement/form/delete/{iPdfId}/{eventId}",requirements = {"iPdfId":"[0-9]+","eventId":"[0-9]+"}, name="_deleteEvenementPdf")
     * Delete the Pdf related to the Evenement.
     */
    public function deleteEvenementPdfAction($iPdfId ,$eventId)
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $oPdfManager = $oEntityManager->getRepository("ZhongfuBlogBundle:FormSeminarFile");
        $oPdf = $oPdfManager->findOneById($iPdfId);

        if (file_exists($this->container->getParameter('upload_base_dir') . $oPdf->getPath() . '/' . $oPdf->getFile())) {
            unlink($this->container->getParameter('upload_base_dir') . $oPdf->getPath() . '/' . $oPdf->getFile());
        }

        rmdir($this->container->getParameter('upload_base_dir') . $oPdf->getPath());

        $oEntityManager->remove($oPdf);
        $oEntityManager->flush();

        return $this->redirect($this->generateUrl('_evenementPdfModify', array('id' => $eventId)));
    }

    /**
     * @Route("/admin/evenement/form/modify/{id}",requirements = {"id":"[0-9]+"}, name="_evenementPdfModify")
     * @Template()
     */
    public function modifyEvenementPdfAction(Request $request, $id)
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $oEvenementManager = $oEntityManager->getRepository("ZhongfuBlogBundle:Evenement");
        $oEvenement = $oEvenementManager->findOneById($id);

        //Generate the form
        $oPdf = new FormSeminarFile();
        $oForm = $this->createForm(new FormSeminarFileType(), $oPdf);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            if ($oForm->isValid()) {

                //image manager to use the name generator
                $oImageManager = $this->get('ImageManager');

                $oFile = $oForm['file']->getData();
                $oEntityManager = $this->getDoctrine()->getManager();

                $oEvenementManager = $oEntityManager->getRepository("ZhongfuBlogBundle:Evenement");
                $oEvenement = $oEvenementManager->findOneById($id);


                //Set the path depending of the post type
                $oPdf->setPath("pdf/evenement/" . $oEvenement->getType()->getUrl() . "/" . $oEvenement->getId());

                //generate a random prefix
                $sPdfName = $oImageManager->generateRandomPrefix($oFile->getClientOriginalName());
                $uploadDir = $this->container->getParameter('upload_base_dir');
                $oPdf->setFile($sPdfName);

                //moving to the directory
                $oFile->move($uploadDir . $oPdf->getPath(), $sPdfName);

                $oEntityManager->persist($oPdf);
                $oEvenement->setFormSeminarFile($oPdf);
                $oEntityManager->flush();

                //unseting to avoid reload or back to page
                unset($oPdf);
                unset($oForm);

                return $this->redirect($this->generateUrl('_evenementPdfModify', array('id' => $id)));
            }
        }


        return ['evenement' => $oEvenement, 'form' => $oForm->createView()];
    }


}
