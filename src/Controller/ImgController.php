<?php

namespace App\Controller;

use App\Entity\Img;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImgController extends AbstractController
{
    /**
     * @Route("/api/img/{id}", name="api_img")
     * @param $id
     * @return BinaryFileResponse
     */
    public function index($id)
    {
       $img = $this->getDoctrine()->getRepository(Img::class)->find($id);
       return $this->file($img->getPath());
    }
}
