<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Entity\MessageSlack;
use AppBundle\Entity\ChannelSlack;
use AppBundle\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer as JMS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller
{

    /**
     * @Route("/signup", name="signup")
     *
     */
    public function signupAction(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirectToRoute('homepage');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $role = $em->getRepository(Role::class)->findOneByCode('ROLE_REGISTERED');
            $user->setRole($role);

            $em->persist($user);
            $em->flush();
            $this->addFlash('is-success', "L'inscription s'est déroulée avec succès, vous pouvez maintenant vous connecter");
            return $this->redirectToRoute('login');
        }
        return $this->render('user/signup.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/profile", name="edit_profile")
     */
    public function editProfileAction(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['edit' => true]);
        $currentPassword = $user->getPassword();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if(!$user->getPassword())
            {
                $user->setPassword($currentPassword);
            }
            else {
                $password = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
            }
            $em->flush();
            $this->addFlash('is-success', 'Votre profile a été édité avec succès');
        }
        return $this->render('user/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/api/me", name="get_user_info")
     * @Method({"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function getUserAction()  {
        $user = $this->getUser();
        $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');
        $wsToken = $jwtManager->create($user);
        $data = ['user' => $user, 'token' => $wsToken];
        $serializer = JMS\SerializerBuilder::create()->build();
        $data = $serializer->serialize($data, 'json');
        return new JsonResponse($data);
    }    
}
