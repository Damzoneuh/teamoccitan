<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\MailHelper;
use App\Helper\UserHelper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    use UserHelper;
    use MailHelper;

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return mixed
     * @throws Exception
     * @throws TransportExceptionInterface
     * @Route("/register", name="register")
     */
    public function register(Request $request, MailerInterface $mailer, UserPasswordEncoderInterface $passwordEncoder){
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Confirmez votre mot de passe',
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn tbn-group btn-success'
                ]
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            if ($data['password'] == $data['plainPassword']){
                $em = $this->getDoctrine()->getManager();
                $user = new User();

                $user->setEmail($data['email']);
                $user->setName($data['name']);
                $user->setLastname($data['lastName']);
                $user->setPassword(
                    $passwordEncoder->encodePassword($user, $data['password'])
                );
                $token = $this->getResetToken();
                $user->setResetToken($token);
                $user->setIsValidated(false);

                $em->persist($user);
                $em->flush();

                $message = $this->createTemplatedMail(
                    'Confirmation d\'inscription',
                    'account@' . $request->getHost(),
                    $user->getEmail(),
                    'email/register.html.twig',
                    [
                        'uri' => 'https://' . $request->getHost() . '/reset/' . $token,
                        'subject' => 'Confirmation d\'inscription',
                        'btnContent' => 'Confirmer mon compte',
                        'content' => 'Vous venez de créer un compte sur le site de la team occitans, afin de vérifier que vous n\'êtes pas un robot nous vous invitons à cliquer sur ce lien afin de valider votre compte .'
                    ]
                );
                $mailer->send($message);
                $this->addFlash('success', 'Un mail viens d\'être envoyé à l\'adresse ' . $user->getEmail());
                return $this->redirectToRoute('index');
            }

            $this->addFlash('error', 'Les mots de passe ne sont pas identiques');
            return $this->redirectToRoute('register');
        }

        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param $token
     * @return RedirectResponse|Response
     * @Route("/reset/{token}", name="reset_after_mail")
     */
    public function resetPassword(Request $request,UserPasswordEncoderInterface $passwordEncoder, $token){
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['resetToken' => $token]);
        if (!$user){
            $this->addFlash('error', 'Le jeton de validation est introuvable, si le problème persiste merci de contacter un administrateur');
            return $this->redirectToRoute('index');
        }
        if (!$user->getIsValidated()){
            $user->setIsValidated(true);
            $user->setResetToken(null);
            $em->flush();
            $this->addFlash('success', 'Votre compte à bien été validé, vous pouvez dès à présent vous connecter');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createFormBuilder()
            ->add('password', PasswordType::class, [
                'label' => 'Tapez votre nouveau mot de passe',
                'required' => true
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Confirmez votre nouveau mot de passe'
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
            if ($data['password'] == $data['plainPassword']){
                $user->setPassword($passwordEncoder->encodePassword($user, $data['password']));
                $user->setResetToken(null);
                $em->flush();
                $this->addFlash('success', 'Votre nouveau mot de passe à été pris en compte');
                return $this->redirectToRoute('app_logout');
            }
            return $this->redirectToRoute('index');
        }

        return $this->render('security/reset.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws Exception
     * @throws TransportExceptionInterface
     * @Route("/reseting/request", name="reset_request")
     */
    public function resetPasswordRequest(Request $request, MailerInterface $mailer){
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true
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
            $em = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if (!$user){
                $this->addFlash('error', 'L\'email ne correspond à aucun utilisateur');
                return $this->redirectToRoute('reset_request');
            }
            $token = $this->getResetToken();
            $user->setResetToken($token);
            $em->flush();
            $message = $this->createTemplatedMail(
                'Réinitialisation mot de passe',
                'account@' . $request->getHost(),
                $user->getEmail(),
                'email/register.html.twig',
                [
                    'uri' => 'https://' . $request->getHost() . '/reset/' . $token,
                    'subject' => 'Réinitialisation mot de passe',
                    'btnContent' => 'Réinitialiser mon mot de passe',
                    'content' => 'Vous avez fait une demande de réinitialisation de mot de passe, si vous n\'êtes pas à l\'origine de cette demande vous pouvez ignorer ce message . Sinon nous vous invitons à cliquer sur le lien ci-dessous .'
                ]
            );
            $mailer->send($message);
            $this->addFlash('success', 'Un email contenant un lien de réinitialisation à été envoyé à l\'adresse ' . $user->getEmail());
            return $this->redirectToRoute('index');
        }

        return $this->render('security/reset-request.html.twig', ['form' => $form->createView()]);
    }
}
