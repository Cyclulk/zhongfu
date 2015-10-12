<?php

namespace Zhongfu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Zhongfu\BlogBundle\Entity\Image;
use Zhongfu\BlogBundle\Form\ImageType;

class AdminImageController extends Controller
{
     /**
     * @Route("/admin/{postType}/image/delete/{id}",requirements = {"id":"[0-9]+","postType":"[a-z]+"},defaults={"comeFromUploader" = "false"}, name="_deleteImage")
     * Delete the image related to the post.
     */
    public function deleteImageAction($id, $postType)
    {
        $sPostType = ucfirst($postType);
        $oEntityManager = $this->getDoctrine()->getManager();
        $oImageManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Image');
        $oImage = $oImageManager->findOneById($id);

        if (file_exists($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/' . $oImage->getFile())) {
            unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/' . $oImage->getFile());
        }
        if (file_exists($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile())) {
            unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile());
        }

        $sMethod = 'get' . $sPostType;
        $sPostId = $oImage->$sMethod()->getId();
        $oEntityManager->remove($oImage);
        $oEntityManager->flush();

        return $this->redirect($this->generateUrl('_postsImageModifier', array('id' => $sPostId, 'postType' => strtolower($sPostType))));
    }


    /**
     * @Route("/admin/{postType}/image/set/{id}",requirements = {"id":"[0-9]+","postType":"[a-z]+"}, name="_setImage")
     * Apply the image as avatar for the selected post.
     */
    public function setImageAction($id, $postType)
    {
        $sPostType = ucfirst($postType);
        $oEntityManager = $this->getDoctrine()->getManager();
        $oImageManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Image');
        $oPostManager = $oEntityManager->getRepository("ZhongfuBlogBundle:$sPostType");

        $oImage = $oImageManager->findOneById($id);
        $sMethod = "get" . $postType;

        //Setting the filename of the image to the image variable of the post
        $oPost = $oPostManager->findOneById($oImage->$sMethod())->setAvatar($oImage);

        $oEntityManager->persist($oPost);
        $oEntityManager->flush();

        //generate url to go back to the list of the post type
        $sUrl = '_' . strtolower($sPostType) . 'List';

        return $this->redirect($this->generateUrl('_postsImageModifier', array('id' => $oPost->getId(), 'postType' => strtolower($sPostType))));
    }

    /**
     * @Route("/admin/{postType}/image/modify/{id}",requirements = {"id":"[0-9]+","postType":"[a-z]+"}, name="_postsImageModifier")
     * @Template()
     */
    public function modifyImageAction($id, $postType, Request $request)
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $sPostType = ucfirst($postType);

        //Generate the form
        $oImage = new Image();
        $oForm = $this->createForm(new ImageType(), $oImage);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            if ($oForm->isValid()) {

                //Loading service
                $oImageManager = $this->get('ImageManager');

                $oFile = $oForm['file']->getData();
                $oEntityManager = $this->getDoctrine()->getManager();

                $oPostManager = $oEntityManager->getRepository("ZhongfuBlogBundle:$sPostType");
                $oPost = $oPostManager->findOneById($id);

                //Set the id and type for the link of the image
                $oImage->{'set' . $sPostType}($oPost);

                //Set the path depending of the post type
                $oImage->setPath("images/" . strtolower($sPostType) . "/" . $oPost->getType()->getUrl() . "/" . $oPost->getId());

                //generate a random prefix
                $sImageName = $oImageManager->generateRandomPrefix($oFile->getClientOriginalName());
                $uploadDir = $this->container->getParameter('upload_base_dir');
                $oImage->setFile($sImageName);

                //moving to the directory
                $oFile->move($uploadDir . $oImage->getPath(), $sImageName);

                //creating the miniature
                $oImageManager->resize( $uploadDir . $oImage->getPath() . "/" . $sImageName);

                //resize
                $oImageManager->makeMiniature($uploadDir . $oImage->getPath() . "/" . $sImageName);

                $oEntityManager->persist($oImage);
                $oEntityManager->flush();

                //unseting to avoid reload or back to page
                unset($oImage);
                unset($oForm);

                return $this->redirect($this->generateUrl('_postsImageModifier', array('id' => $id, 'postType' => strtolower($sPostType))));
            }
        }

        //Get the Images related to the ID of the article
        $iIdOfAvatar = 0;
        $oPostManager = $oEntityManager->getRepository("ZhongfuBlogBundle:$sPostType");
        $oPost = $oPostManager->findOneById($id);
        $aImages = $oPost->getImagesList();
        if (null != $oPost->getAvatar()) {
            $iIdOfAvatar = $oPost->getAvatar()->getId();
        }
        return ['post' => $oPost, 'images' => $aImages, 'form' => $oForm->createView(), 'postType' => strtolower($sPostType), 'idOfAvatar' => $iIdOfAvatar];
    }
}
