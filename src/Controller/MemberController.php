<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @param Request $request
     * @Route("/admin/event/create", name="admin_event_create")
     * @return RedirectResponse|Response
     */
    public function createEvent(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'évènement'
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date',
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                    'hour' => 'Heure', 'minute' => 'Minute'
                ],
                'input_format' => 'd-m-Y H:i',
                'attr' => [
                    'class' => 'd-flex justify-content-around'
                ]
            ])
            ->add('duration', NumberType::class, [
                'label' => 'Durée'
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
            $em = $this->getDoctrine()->getManager();
            $event = new Event();
            $data = $form->getData();
            $event->setName($data['name']);
            $event->setDate($data['date']);
            $event->setDuration($data['duration']);
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'L\'évènement à été ajouté');
            return $this->redirectToRoute('admin_event_create');
        }

        return $this->render('member/create-event.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param null $id
     * @return JsonResponse
     * @Route("/api/user/event", name="api_user_event", methods={"GET"})
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
}
