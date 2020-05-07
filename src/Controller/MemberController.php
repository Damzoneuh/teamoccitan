<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Img;
use App\Entity\Result;
use App\Entity\Skin;
use App\Entity\User;
use App\Helper\FileHelper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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

class MemberController extends AbstractController
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
     * @Route("/member", name="member")
     */
    public function index()
    {
        return $this->render('member/index.html.twig');
    }

    /**
     * @param null $id
     * @return JsonResponse
     * @Route("/api/user/event/{id}", name="api_user_event", methods={"GET"})
     */
    public function getEvent($id = null){
        if (!$id){
            return $this->json(
                $this->getDoctrine()->getRepository(Event::class)->findAll(),
                200,
                [],
                $this->context
            );
        }

        return $this->json(
            $this->getDoctrine()->getRepository(Event::class)->find($id),
            200,
            [],
            $this->context
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route("/pilot/result/create", name="pilot/result/create")
     */
    public function createResult(Request $request){
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Nom de la course'
            ])
            ->add('position', NumberType::class, [
                'label' => 'Position'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la course'
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
            $em = $this->getDoctrine()->getManager();
            $result = new Result();
            $img  = new Img();
            $data = $form->getData();
            $file = $data['file'];
            $randomName = $this->getGeneratedName($file);
            $storagePath = $this->getParameter('app.storage');
            $path = $storagePath . '/' . $randomName;
            $this->moveFile($file, $storagePath, $randomName);
            $img->setName($this->getFileName($file));
            $img->setPath($path);
            $em->persist($img);

            /** @var User $user */
            $user = $this->getUser();

            $result->setName($data['name']);
            $result->setDescription($data['description']);
            $result->setPosition($data['position']);
            $result->setImg($img);
            $result->setAuthor($user->getName() . ' ' . $user->getLastname());

            $em->persist($result);
            $em->flush();

            $this->addFlash('success', 'Votre résultat à été ajouté');
            return $this->redirectToRoute('member');
        }
        return $this->render('result/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @return JsonResponse
     * @Route("/member/api/result", name="member_api_result", methods={"GET"})
     */
    public function getLastResults(){
        return $this->json(
            $this->getDoctrine()->getRepository(Result::class)->findBy([], ['id' => 'DESC'], 10),
            200,
            [],
            $this->context
        );
    }

    /**
     * @return JsonResponse
     * @Route("/pilot/api/skin", name="/pilot/api/skin", methods={"GET"})
     */
    public function getSkins(){
        return $this->json(
            $this->getDoctrine()->getRepository(Skin::class)->findAll(),
            200,
            [],
            $this->context
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     * @Route("/admin/skin/create", name="admin_skin_create")
     */
    public function createSkin(Request $request){
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Nom du skin'
            ])
            ->add('file', FileType::class, [
                'label' => 'Fichier'
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
            $data = $form->getData();
            $file = $data['file'];
            $randomName = $this->getGeneratedName($file);
            $storagePath = $this->getParameter('app.storage');
            $this->moveFile($file, $storagePath, $randomName);

            $skin = new Skin();
            $skin->setName($data['name']);
            $skin->setPath($storagePath . '/' . $randomName);

            $em->persist($skin);
            $em->flush();

            $this->addFlash('success', 'Le skin à été ajouté');
            return $this->redirectToRoute('admin_skin_create');
        }

        return $this->render('skin/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @Route("/pilot/download/skin/{id}", name="pilot_download_skin")
     */
    public function downloadSkin($id){
        /** @var Skin $skin */
        $skin = $this->getDoctrine()->getRepository(Skin::class)->find($id);
        return $this->file($skin->getPath());
    }
}
