<?php
namespace AppBundle\Controller\Admin;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/admin/user")
 */
class UserAdminController extends  Controller
{
    /**
     * @Route("/list", name="user_list")
     */
    public function listAction(EntityManagerInterface $em)
    {

        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/user/list.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @Route("/edit/{id}", name="user_edit")
     */
    public function editAction(EntityManagerInterface $em, User $user, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserType::class, $user, ['edit_admin' => true]);
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
            $this->addFlash('is-success', 'Modifié avec succès');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}