<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Role;
use AppBundle\Form\RoleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/role")
 */
class RoleAdminController extends Controller
{
    /**
     * @Route("/list", name="role_list")
     */
    public function listAction(EntityManagerInterface $em)
    {
        $roles = $em->getRepository(Role::class)->findAll();
        return $this->render('admin/role/list.html.twig', [
            'roles' => $roles,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_role")
     */
    public function editAction(Request $request, EntityManagerInterface $em, Role $role)
    {
        $form = $this->createForm(RoleType::class, $role);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $this->addFlash('is-success', 'Modifié avec succès');
        }
        return $this->render('admin/role/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}