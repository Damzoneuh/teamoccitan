<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @return Response
     * @Route("/editor", name="editor")
     */
    public function editor(){
        return $this->render('admin/editor.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/user", name="admin_all_users")
     */
    public function getUsers(){
        return $this->render('admin/users.html.twig', [
            'users' => $this->getDoctrine()->getRepository(User::class)->findBy(['isValidated' => true])
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/admin/user/{id}", name="admin_user")
     * @return RedirectResponse|Response
     */
    public function getSelectedUser(Request $request, $id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $form = $this->createFormBuilder()
            ->add('role', ChoiceType::class, [
                'choices' => [
                    '' => '',
                    'membre' => 'ROLE_USER',
                    'admin' => 'ROLE_ADMIN',
                    'editeur' => 'ROLE_EDITOR',
                    'pilote' => 'ROLE_PILOT'
                ],
                'required' => true,
                'label' => 'Choix du role'
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
            /** @var User $user */
            $user->setRoles([$data['role']]);
            $em->flush();

            $this->addFlash('success', 'Le role a été ajouté');
            return $this->redirectToRoute('admin_all_users');
        }
        return $this->render('admin/user.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
