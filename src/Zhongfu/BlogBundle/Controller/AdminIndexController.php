<?php

namespace Zhongfu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


class AdminIndexController extends Controller
{

    /**
     * @Route("/admin/", name="_adminIndex")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }
}