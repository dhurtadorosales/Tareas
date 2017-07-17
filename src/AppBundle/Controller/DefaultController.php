<?php

namespace AppBundle\Controller;

use AppBundle\Services\Helpers;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }


    public function pruebasAction() {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('BackendBundle:User');
        $users = $userRepo->findAll();

        $helpers = $this->get(Helpers::class);
        echo $helpers->holaMundo();
        die();
        return $this->json([
            'status' => 'success',
            'users' => $users[0]->getName()
        ]);
    }
}
