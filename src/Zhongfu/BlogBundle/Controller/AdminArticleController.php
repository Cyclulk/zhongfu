<?php

namespace Zhongfu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Zhongfu\BlogBundle\Entity\Article;
use Zhongfu\BlogBundle\Entity\Image;
use Zhongfu\BlogBundle\Form\ArticleType;
use Zhongfu\BlogBundle\Form\ImageType;
use Zhongfu\MediaManagerBundle\ImageManager;

class AdminArticleController extends Controller
{

    /**
     * @Route("/admin/article/add", name="_addArticle")
     * @Template()
     */
    public function addArticleAction(Request $request)
    {
        $oArticle = new Article();
        $oForm = $this->createForm(new ArticleType(), $oArticle);

        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);

            /**
             * @var $oArticle Article
             */
            if ($oForm->isValid()) {
                $oEntityManager = $this->getDoctrine()->getManager();
                $oEntityManager->persist($oArticle);
                $oEntityManager->flush();

                $id = $oArticle->getId();

                unset($oArticle);
                unset($oForm);
                return $this->redirect($this->generateUrl('_postsImageModifier', array('id' => $id, 'postType' => "article")));
            }
        }

        return ['form' => $oForm->createView()];
    }

    /**
     * @Route("/admin/article/modify/{id}",requirements = {"id":"[0-9]+"}, defaults={"id" = 0}, name="_articleModify")
     * @Template()
     */
    public function modifyArticleAction(Request $request, $id)
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $oArticleManager = $oEntityManager->getRepository("ZhongfuBlogBundle:Article");
        $oArticle = $oArticleManager->findOneById($id);

        $oForm = $this->createForm(new ArticleType(), $oArticle);

        /**
         * @var $oArticle Article
         */
        if ($request->getMethod() == 'POST') {
            $oForm->handleRequest($request);
            /**
             * @var $oArticle Article
             */
            if ($oForm->isValid()) {

                $oEntityManager->persist($oArticle);
                $oEntityManager->flush();

                unset($oArticle);
                unset($oForm);

                return $this->redirect($this->generateUrl('_articleList'));
            }
        }
        return ['form' => $oForm->createView(), 'article' => $oArticle];
    }

    /**
     * @Route("/admin/article/list",name="_articleList")
     * @Template()
     */
    public function listArticleAction()
    {
        $oEntityManager = $this->getDoctrine()->getManager();
        $oArticleManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Article');
        $aArticlesList = $oArticleManager->findBy(array(), array('date' => 'desc'));

        return ['articles' => $aArticlesList];
    }

    /**
     * @Route("/admin/article/delete/{id}",requirements = {"id":"[0-9]+"},name="_deleteArticle")
     */
    public function deleteArticleAction($id)
    {

        $oEntityManager = $this->getDoctrine()->getManager();
        $oArticleManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Article');

        $oArticle = $oArticleManager->findOneById($id);

        //We delete all the image related to the article
        /**
         * @var $oArticle Article
         */
        foreach ($oArticle->getImagesList() as $oImage) {

            if (file_exists($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/' . $oImage->getFile())) {
                unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/' . $oImage->getFile());
            }
            if (file_exists($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile())) {
                unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile());
            }
            $oEntityManager->remove($oImage);
        }
        //and the avatar
        if (file_exists($this->container->getParameter('upload_base_dir') . $oArticle->getAvatar()->getPath() . '/' . $oImage->getFile())) {
            unlink($this->container->getParameter('upload_base_dir') . $oArticle->getAvatar()->getPath() . '/' . $oImage->getFile());
        }
        if (file_exists($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile())) {
            unlink($this->container->getParameter('upload_base_dir') . $oImage->getPath() . '/mini_' . $oImage->getFile());
        }

        rmdir($this->container->getParameter('upload_base_dir') . $oImage->getPath());

        $oEntityManager->remove($oImage);
        $oEntityManager->remove($oArticle);
        $oEntityManager->flush();

        return $this->redirect($this->generateUrl('_articleList'));
    }
}