<?php

namespace App\Controller;

use App\Entity\Img;
use App\Entity\News;
use App\Entity\User;
use App\Helper\FileHelper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class NewsController extends AbstractController
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
     * @Route("/news/{id}", name="news")
     * @param null $id
     * @return Response
     */
    public function index($id = null)
    {
        if ($id){
            return $this->render('news/show.html.twig', ['news' => $this->getDoctrine()->getRepository(News::class)->find($id)]);
        }
        return $this->render('news/index.html.twig');
    }

    /**
     * @param null $id
     * @return JsonResponse
     * @Route("/api/news/{id}", name="api_news", methods={"GET"})
     */
    public function getNews($id = null){
        if ($id){
            return $this->json($this->getDoctrine()->getRepository(News::class)->find($id), 200, [], $this->context);
        }
        return $this->json($this->getDoctrine()->getRepository(News::class)->findBy(['isActive' => true], ['id' => 'DESC'], 9),200, [], $this->context);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     * @Route("/admin/news/create", name="admin_news_create")
     */
    public function createNews(Request $request){
        $form = $this->createFormBuilder()
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Texte'
            ])
            ->add('file', FileType::class, [
                'label' => 'Image',
                'required' => false
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
            $em = $this->getDoctrine()->getManager();
            $img = null;
            $news = new News();
            $data = $form->getData();
            if ($data['file']){
                $img = new Img();
                $name = $this->getFileName($data['file']);
                $randomName = $this->getGeneratedName($data['file']);
                $path = $this->getParameter('app.storage');
                if (!$this->moveFile($data['file'], $path, $randomName)){
                    $this->addFlash('error','Une erreur est survenue lors de l\'envoi du fichier');
                    return $this->redirectToRoute('admin_news_create');
                }
                $img->setPath($path . '/' . $randomName);
                $img->setName($name);
                $em->persist($img);
                $em->flush();

                $news->setImg($img);
            }
            /** @var User $user */
            $user = $this->getUser();
            $news->setAuthor($user);
            $news->setTitle($data['title']);
            $news->setText($data['text']);
            $news->setIsActive(true);
            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'L\'actualité à bien été enregistrée');
            return $this->redirectToRoute('admin_news_create');
        }
        return $this->render('news/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @return Response
     * @Route("/admin/news", name="admin_news")
     */
    public function getAdminNews(){
        return $this->render('news/admin.html.twig', ['news' => $this->getDoctrine()->getRepository(News::class)->findAll()]);
    }

    /**
     * @param $id
     * @return RedirectResponse
     * @Route("admin/news/delete/{id}", name="admin_news_delete")
     */
    public function deleteNews($id){
        $em = $this->getDoctrine()->getManager();
        /** @var News $news */
        $news = $em->getRepository(News::class)->find($id);
        $em->remove($news);
        $em->flush();
        if ($news->getImg()){
            /** @var Img $img */
            $img = $em->getRepository(Img::class)->find($news->getImg()->getId());
            $this->removeFile($img->getPath());
            $em->remove($img);
            $em->flush();
        }
        $this->addFlash('success', 'L\'actualité à été supprimée');
        return $this->redirectToRoute('admin_news');
    }

    /**
     * @param $id
     * @return RedirectResponse
     * @Route("/admin/news/enable/{id}", name="admin_news_enable")
     */
    public function enableNews($id){
        $em = $this->getDoctrine()->getManager();
        /** @var News $news */
        $news = $em->getRepository(News::class)->find($id);
        if ($news->getIsActive()){
            $news->setIsActive(false);
            $em->flush();
            $this->addFlash('success', 'L\'actualité à bien été désactivée');
            return $this->redirectToRoute('admin_news');
        }
        $news->setIsActive(true);
        $em->flush();
        $this->addFlash('success', 'L\'actualité à bien été activée');
        return $this->redirectToRoute('admin_news');
    }
}
