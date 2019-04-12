<?php
namespace AppBundle\Controller;
use AppBundle\Service\InitWorkspace;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WorkspaceController extends Controller
{
    /**
     * @Route("/workspace/new", name="new_workspace")
     */
    public function newAction(Request $request, InitWorkspace $initWorkspace)
    {
        $code = $request->query->get('code', false);
        $result = $initWorkspace->initData($code, $this->container->getParameter('clientId'), $this->container->getParameter('clientSecret'));
        if($result)
        {
            $this->addFlash('is-success', 'L\'importation est terminée');
            return $this->redirectToRoute('admin_homepage');
        }
        else {
            $this->addFlash('is-danger', 'Une erreur s\'est produite :/');
            return $this->redirectToRoute('admin_homepage');
        }
    }

}
