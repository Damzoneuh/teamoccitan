<?php

namespace App\Controller;

use App\Entity\Galery;
use App\Entity\Img;
use App\Helper\FileHelper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GaleryController extends AbstractController
{
    use FileHelper;
    private $context;
    private $serializer;
    public function __construct()
    {
        $this->context = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object;
            },
        ];
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }
    /**
     * @Route("/gallery", name="gallery")
     */
    public function index()
    {
        return $this->render('galery/index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     * @Route("/editor/gallery/create", name="editor_gallery_create")
     */
    public function CreateImg(Request $request){
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('file', FileType::class, [
                'label' => 'Image'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-group btn-success'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $name = $data['name'];
            $file = $data['file'];
            $em = $this->getDoctrine()->getManager();

            $storagePath = $this->getParameter('app.storage');
            $randomName = $this->getGeneratedName($file);
            $img = new Img();
            $gallery = new Galery();
            $this->moveFile($file, $storagePath, $randomName);
            $img->setName($name);
            $img->setPath($storagePath . '/' . $randomName);
            $em->persist($img);
            $gallery->setImg($img);
            $em->persist($gallery);
            $em->flush();

            $this->addFlash('success', 'Votre image à bien été ajoutée');
            return $this->redirectToRoute('editor_gallery_create');
        }
        return $this->render('galery/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @return JsonResponse
     * @Route("/api/gallery", name="api_gallery")
     */
    public function getGallery(){
        return $this->json(
            $this->getDoctrine()->getRepository(Galery::class)->findBy([], ['id' => 'DESC']),
            200,
            [],
            $this->context
        );
    }
}
