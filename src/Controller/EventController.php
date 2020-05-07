<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Event;
use App\Entity\Img;
use App\Entity\Relay;
use App\Entity\Team;
use App\Entity\Track;
use App\Entity\User;
use App\Helper\FileHelper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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

class EventController extends AbstractController
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

    use FileHelper;
    /**
     * @Route("/event", name="event")
     */
    public function index()
    {
        return $this->render('event/index.html.twig');
    }


    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/track/create", name="admin_track_create")
     * @throws Exception
     */
    public function createCircuit(Request $request){
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
            $em = $this->getDoctrine()->getManager();
            $img = new Img();
            $track = new Track();
            $data = $form->getData();
            $fileName = $this->getGeneratedName($data['file']);
            $path = $this->getParameter('app.storage');
            $this->moveFile($data['file'], $path, $fileName);

            $img->setName($data['name']);
            $img->setPath($path . '/' . $fileName);
            $em->persist($img);
            $track->setName($data['name']);
            $track->setImg($img);
            $em->persist($track);
            $em->flush();

            $this->addFlash('success', 'Le circuit à bien été ajouté');
            return $this->redirectToRoute('admin_track_create');
        }

        return $this->render('event/create-track.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route("/admin/car/create", name="admin_car_create")
     */
    public function createCar(Request $request){
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
            $em = $this->getDoctrine()->getManager();
            $img = new Img();
            $car = new Car();
            $data = $form->getData();
            $fileName = $this->getGeneratedName($data['file']);
            $path = $this->getParameter('app.storage');
            $this->moveFile($data['file'], $path, $fileName);

            $img->setName($data['name']);
            $img->setPath($path . '/' . $fileName);
            $em->persist($img);
            $car->setName($data['name']);
            $car->setImg($img);
            $em->persist($car);
            $em->flush();

            $this->addFlash('success', 'La voiture à bien été ajouté');
            return $this->redirectToRoute('admin_car_create');
        }

        return $this->render('event/create-car.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @Route("/admin/event/create", name="admin_event_create")
     * @return RedirectResponse|Response
     */
    public function createEvent(Request $request)
    {
        $tracks = $this->getDoctrine()->getRepository(Track::class)->findAll();
        $cars = $this->getDoctrine()->getRepository(Car::class)->findAll();

        $choicesTrack = [];
        $choiceCars = [];
        foreach ($cars as $car){
            $choiceCars[$car->getName()] = $car->getId();
        }
        foreach ($tracks as $track){
            $choicesTrack[$track->getName()] = $track->getId();
        }

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
            ->add('cars', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => $choiceCars,
                'label' => 'Voitures disponibles'
            ])
            ->add('track', ChoiceType::class, [
                'choices' => $choicesTrack,
                'label' => 'Circuit'
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
            foreach ($data['cars'] as $car){
                $event->addCar($em->getRepository(Car::class)->find($car));
            }
            $event->setName($data['name']);
            $event->setDate($data['date']);
            $event->setDuration($data['duration']);
            $event->addTrack($em->getRepository(Track::class)->find($data['track']));
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'L\'évènement à été ajouté');
            return $this->redirectToRoute('admin_event_create');
        }

        return $this->render('member/create-event.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param $pilot
     * @param $eventId
     * @return RedirectResponse
     * @Route("/pilot/event/pilot/{pilot}/{eventId}", name="pilot_event_pilot")
     */
    public function addPilot($pilot, $eventId){
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->find($pilot);
        /** @var Event $event */
        $event = $this->getDoctrine()->getRepository(Event::class)->find($eventId);
        $em = $this->getDoctrine()->getManager();
        $event->addPilotEngage($user);

        $em->flush();

        $this->addFlash('success', 'Votre inscription à été prise en compte');
        return $this->redirectToRoute('member');
    }

    /**
     * @param $id
     * @return Response
     * @Route("/pilot/event/{id}")
     */
    public function showEvent($id){
        return $this->render('event/show.html.twig', ['event' => $id]);
    }

    /**
     * @param null $id
     * @return JsonResponse
     * @Route("/pilot/api/team/{id}", name="pilot_api_team", methods={"GET"})
     */
    public function getTeams($id = null){
        if (!$id){
            return $this->json($this->getDoctrine()->getRepository(Team::class)->findAll(),
                200,
                [],
                $this->context
            );
        }
        return $this->json($this->getDoctrine()->getRepository(Team::class)->find($id),
            200,
            [],
            $this->context
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/pilot/api/relay/create", name="pilot_api_relay_create", methods={"POST"})
     */
    public function createRelay(Request $request){
        $data = $this->serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        $relay = new Relay();
        /** @var Car $car */
        $car = $em->getRepository(Car::class)->find($data['car']);
        /** @var User $pilot */
        $pilot = $em->getRepository(User::class)->find($data['pilot']);
        /** @var Team $team */
        $team = $em->getRepository(Team::class)->find($data['team']);
        $relay->setCar($car);
        $relay->setTeam($team);
        $relay->setPilot($pilot);
        $relay->setTimeOffset($data['offset']);

        $em->persist($relay);
        /** @var Event $event */
        $event = $em->getRepository(Event::class)->find($data['event']);

        $event->addRelay($relay);
        $em->flush();

        return $this->json('success');
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/pilot/api/relay/delete/{id}", name="pilot_api_relay_delete", methods={"DELETE"})
     */
    public function deleteRelay($id){
        $em = $this->getDoctrine()->getManager();
        $relay = $em->getRepository(Relay::class)->find($id);
        $em->remove($relay);
        $em->flush();

        return $this->json(true);
    }
}

