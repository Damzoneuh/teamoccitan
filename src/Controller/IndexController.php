<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @param $name
     * @return BinaryFileResponse
     * @Route("/file/{name}", name="render_file")
     */
    public function renderFile($name){
        return $this->file($this->getParameter('app.storage') . '/' . $name);
    }
}
