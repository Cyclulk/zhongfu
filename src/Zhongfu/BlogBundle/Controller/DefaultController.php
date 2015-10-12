<?php

namespace Zhongfu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    const DEFAULT_ARTICLE_LIMIT = 3;
    const DEFAULT_EVENEMENT_LIMIT = 5;

    /**
     * @Route("/",name="_homepage")
     * @Template()
     */
    public function indexAction($iLimitArticles = self::DEFAULT_ARTICLE_LIMIT)
    {
        $aTypes = $this->getDoctrine()->getManager()->getRepository('ZhongfuBlogBundle:Type')->findAll();

        $oEntityManager = $this->getDoctrine()->getManager();
        $oArticleManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Article');
        $aArticles = $oArticleManager->findBy(array(), array('date'=>'desc'), $iLimitArticles, 0);

        $query = $oEntityManager->createQuery('SELECT e FROM ZhongfuBlogBundle:Evenement e WHERE e.date > CURRENT_DATE()');
        $aEvenements = $query->getResult();

        $oPoemeSelected = $this->getPoeme();
        $aPostsVedette = $this->getEnVedette();

        return ['articles' => $aArticles, 'evenements' => $aEvenements, 'poeme' => $oPoemeSelected, 'types' => $aTypes, 'enVedettes' => $aPostsVedette];
    }

    /**
     * @Route("/{category}",requirements = {"category"="[a-z]+"}, name="_categoryHome")
     * @Template()
     */
    public function categoryHomeAction($iLimitArticles = self::DEFAULT_ARTICLE_LIMIT, $iLimitEvenements = self::DEFAULT_EVENEMENT_LIMIT, $category)
    {
        $oEntityManager = $this->getDoctrine()->getManager();

        //Creating array of type of category to check if type exist
        $oTypeManager = $oEntityManager->getRepository("ZhongfuBlogBundle:Type");
        $aType = $oTypeManager->findAll();
        $aTypeNameList = [];

        $aPostsVedette = $this->getEnVedette();
        $oPoemeSelected = $this->getPoeme();

        foreach($aType as $oType){
            $aTypeNameList[] = $oType->getUrl();
        }

        if(in_array($category,$aTypeNameList))
        {
            $oTypeSelected = $oTypeManager->findOneByUrl($category);
            $sIdOfType = $oTypeSelected->getId();

            $oArticleManager = $oEntityManager->getRepository("ZhongfuBlogBundle:Article");
            $aArticles = $oArticleManager->findBy(array('type' => $sIdOfType), array('date'=>'desc'), $iLimitArticles, 0);

            $oEvenementManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Evenement');
            $aEvenements = $oEvenementManager->findBy(array('type' => $sIdOfType), array('date'=>'desc'), $iLimitEvenements, 0);

            return ['articles'=>$aArticles, 'evenements'=>$aEvenements, 'category'=>$oTypeSelected,  'enVedettes' => $aPostsVedette,  'types' => $aType, 'poeme' => $oPoemeSelected];
        }else{
            return $this->redirect($this->generateUrl('_homepage'));
        }
    }

    /**
     * @Route("/{category}/evenement/{id}",requirements = {"category"="[a-z]+","id"="[0-9]+"}, name="_UniqueEvenement")
     * @Template()
     */
    public function uniqueEvenementAction($id){

        $oEntityManager = $this->getDoctrine()->getManager();
        $oEvenementManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Evenement');
        $oArticleManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Article');
        $oEvenement = $oEvenementManager->findOneById($id);

        $oType = $this->getAll('Type');

        $aNeighborsIds = $this->getNeighborsIds($oEvenement);

        $oBefore = $oEvenementManager->findOneById($aNeighborsIds['prev']);
        $oAfter = $oEvenementManager->findOneById($aNeighborsIds['suiv']);

        $aArticlesSameType = $oArticleManager->findBy(array('type' => $oEvenement->getType()), array('date'=>'desc'), $this::DEFAULT_ARTICLE_LIMIT, 0);

        return ['evenement' => $oEvenement, 'types' => $oType, 'articlesSameType' => $aArticlesSameType, 'evenementBefore' => $oBefore, 'evenementAfter' => $oAfter];
    }

    /**
     * @Route("/{category}/article/{id}",requirements = {"category"="[a-z]+","id"="[0-9]+"}, name="_UniqueArticle")
     * @Template()
     */
    public function uniqueArticleAction($id){

        $oEntityManager = $this->getDoctrine()->getManager();
        $oArticleManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Article');
        $oArticle = $oArticleManager->findOneById($id);

        $aArticlesSameType = $oArticleManager->findBy(array('type' => $oArticle->getType()), array('date'=>'desc'), $this::DEFAULT_ARTICLE_LIMIT, 0);

        $oType = $this->getAll('Type');

        $aNeighborsIds = $this->getNeighborsIds($oArticle);

        $oBefore = $oArticleManager->findOneById($aNeighborsIds['prev']);
        $oAfter = $oArticleManager->findOneById($aNeighborsIds['suiv']);


        return ['article' => $oArticle, 'types' => $oType, 'articlesSameType' => $aArticlesSameType, 'articleBefore' => $oBefore, 'articleAfter' => $oAfter];
    }

    /**
     * @Route("/email/test",name="_emailTest")
     * @Template()
     */
    public function testEmailAction()
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email moiii')
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody(
                $this->renderView(
                    'ZhongfuBlogBundle:Default:testEmail.html.twig'),
                'text/html')
        ;
        $this->get('mailer')->send($message);

        return [];
    }

    /**
     * @return array
     */
    public function getEnVedette(){

        $oEntityManager = $this->getDoctrine()->getManager();

        $query = $oEntityManager->createQuery('SELECT a FROM ZhongfuBlogBundle:Article a WHERE a.is_vedette = TRUE ');
        $aArticlesVedette = $query->getResult();
        $query = $oEntityManager->createQuery('SELECT a FROM ZhongfuBlogBundle:Evenement a WHERE a.is_vedette = TRUE ');
        $aEvenementsVedette = $query->getResult();

        $aPostsVedette = array_merge($aArticlesVedette,$aEvenementsVedette);

        return $aPostsVedette;
    }

    public function getAll($sType){
        $oEntityManager = $this->getDoctrine()->getManager()->getRepository("ZhongfuBlogBundle:$sType");
        $aObjects = $oEntityManager->findAll();
        return $aObjects;
    }

    /**
     * @Route("/articles/archives",name="_articlesArchives")
     * @Template()
     */
    public function articlesArchivesAction(){

        $oEntityManager = $this->getDoctrine()->getManager();
        $oArticleManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Article');
        $aArticles = $oArticleManager->findBy(array(), array('date'=>'desc'));
        return['articles' => $aArticles ];
    }

    /**
     * @Route("/images/archives",name="_imagesArchives")
     * @Template()
     */
    public function imagesArchivesAction(){

        $oEntityManager = $this->getDoctrine()->getManager();
        $oImageManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Image');
        $aImages = $oImageManager->findBy(array(), array('id'=>'desc'));
        return['images' => $aImages ];
    }

    public function getNeighborsIds($oPost){

        $sClass = join('', array_slice(explode('\\', get_class($oPost)), -1));
        $oEntityManager = $this->getDoctrine()->getManager();

        $aIdsOdPost = $oEntityManager->createQuery("SELECT p.id FROM ZhongfuBlogBundle:$sClass p")->getScalarResult();
        $aIdsOdPost = array_map('current', $aIdsOdPost);
        $aIdsClone = $aIdsOdPost;

        $iPostId = $oPost->getId();

        $aIdsOfNeightbors = array();
        //setting pointeur of array
        while($val=current($aIdsOdPost))
        {
            if($val==$iPostId)
            break;
            next($aIdsOdPost);
        }

        $aIdsOfNeightbors['prev'] = prev($aIdsOdPost);

        while($val=current($aIdsClone))
        {
            if($val==$iPostId)
                break;
            next($aIdsClone);
        }
        $aIdsOfNeightbors['suiv'] = next($aIdsClone);

        return $aIdsOfNeightbors;
    }

    public function getPoeme(){

        $oEntityManager = $this->getDoctrine()->getManager();
        $oPoemeManager = $oEntityManager->getRepository('ZhongfuBlogBundle:Poeme');

        $aPoemes = $oPoemeManager->findAll();
        $iKeyOfSelectedPoeme = rand(0,count($aPoemes)-1);

        return $aPoemes[$iKeyOfSelectedPoeme];
    }
}
