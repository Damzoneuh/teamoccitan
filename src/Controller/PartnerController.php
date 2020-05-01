<?php

namespace App\Controller;

use App\Entity\Img;
use App\Entity\Partner;
use App\Helper\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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


class PartnerController extends AbstractController
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
     * @return JsonResponse
     * @Route("/api/partner")
     */
    public function getPartner(){
        return $this->json(
            $this->getDoctrine()->getRepository(Partner::class)->findAll(),
            200,
            [],
            $this->context
        );
    }

    /**
     * @return Response
     * @Route("/admin/partner", name="admin_partner")
     */
    public function adminPartner(){
        return $this->render('partner/index.html.twig', [
            'partners' => $this->getDoctrine()->getRepository(Partner::class)->findAll()
        ]);
    }

    /**
     * @Route("/admin/partner/create", name="admin_partner_create", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request): Response
    {
        $partner = new Partner();
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('file', FileType::class, [
                'label' => 'Logo',
                'required' => true
            ])
            ->add('text', TextareaType::class, [
                'label' => 'description',
                'required' => true
            ])
            ->add('link', TextType::class, [
                'label' => 'Lien',
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

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $partner = new Partner();
            $partner->setText($data['text']);
            $partner->setName($data['name']);
            $partner->setLink($data['link']);

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

            $partner->setImg($img);
            $em->persist($partner);
            $em->flush();

            $this->addFlash('success', 'Le partenaire à bien été ajouté');
            return $this->redirectToRoute('admin_partner_create');
        }

        return $this->render('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("partner/{id}", name="partner_show", methods={"GET"})
     * @param Partner $partner
     * @return Response
     */
    public function show(Partner $partner): Response
    {
        return $this->render('partner/show.html.twig', [
            'partner' => $partner,
        ]);
    }

    /**
     * @Route("admin/partner/delete/{id}", name="partner_delete", methods={"DELETE"})
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        /** @var Partner $partner */
        $partner = $this->getDoctrine()->getRepository(Partner::class)->find($id);
        $img = $partner->getImg();
        $em = $this->getDoctrine()->getManager();
        $em->remove($img);
        $em->remove($partner);
        $em->flush();

        $this->addFlash('success', 'Le partenaire à bien été supprimé');
        return $this->redirectToRoute('admin_partner');
    }
}
