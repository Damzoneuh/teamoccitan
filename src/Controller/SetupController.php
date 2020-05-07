<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Setup;
use App\Entity\Track;
use App\Helper\FileHelper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

class SetupController extends AbstractController
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
     * @param Request $request
     * @Route("/admin/setup/create", name="admin_setup_create")
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function createSetup(Request $request){
        $tracks = $this->getDoctrine()->getRepository(Track::class)->findAll();
        $tracksChoice = [];
        $cars = $this->getDoctrine()->getRepository(Car::class)->findAll();
        $carsChoice = [];

        /** @var Track $track */
        foreach ($tracks as $track){
            $tracksChoice[$track->getName()] = $track->getId();
        }

        /** @var Car $car */
        foreach ($cars as $car){
            $carsChoice[$car->getName()] = $car->getId();
        }

        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('file', FileType::class, [
                'label' => 'Fichier'
            ])
            ->add('track', ChoiceType::class, [
                'choices' => $tracksChoice,
                'label' => 'Circuit'
            ])
            ->add('car', ChoiceType::class, [
                'choices' => $carsChoice,
                'label' => 'Voiture'
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
            $track = $em->getRepository(Track::class)->find($data['track']);
            $car = $em->getRepository(Car::class)->find($data['car']);
            $file = $data['file'];

            $randomFileName = $this->getGeneratedName($file);
            $storagePath = $this->getParameter('app.storage');
            $this->moveFile($file, $storagePath, $randomFileName);
            $path = $storagePath . '/' . $randomFileName;
            $setup = new Setup();
            $setup->setName($data['name']);
            $setup->setCar($car);
            $setup->setTrack($track);
            $setup->setFile($path);
            $em->persist($setup);
            $em->flush();

            $this->addFlash('success', 'Le setup ' . $setup->getName() . ' à été ajouté');
            return $this->redirectToRoute('admin_setup_create');
        }

        return $this->render('setup/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param $track
     * @param $car
     * @return JsonResponse
     * @Route("/pilot/api/setup/{track}/{car}", name="pilot_api_setup", methods={"GET"})
     */
    public function getSetup($track, $car){
        $setups = $this->getDoctrine()->getRepository(Setup::class)->findBy(['track' => $track, 'car' => $car], ['id' => 'DESC']);
        return $this->json($setups, 200, [], $this->context);
    }

    /**
     * @return JsonResponse
     * @Route("/api/car", name="api_car", methods={"GET"})
     */
    public function getCars(){
        return $this->json(
            $this->getDoctrine()->getRepository(Car::class)->findAll(),
            200,
            [],
            $this->context
        );
    }

    /**
     * @return JsonResponse
     * @Route("/api/track", name="api_track", methods={"GET"})
     */
    public function getTrack(){
        return $this->json(
            $this->getDoctrine()->getRepository(Track::class)->findAll(),
            200,
            [],
            $this->context
        );
    }

    /**
     * @param $id
     * @return BinaryFileResponse
     * @Route("/pilot/setup/download/{id}", name="pilot_setup_download")
     */
    public function downloadSetup($id){
        /** @var Setup $setup */
        $setup = $this->getDoctrine()->getRepository(Setup::class)->find($id);
        return $this->file($setup->getFile());
    }
}
