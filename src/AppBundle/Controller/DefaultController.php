<?php

namespace AppBundle\Controller;

use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }


    public function pruebasAction(Request $request) {
        $helpers = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);
        $token = $request->get('authorization', null);

        $result = $helpers->json([
            'status' => 'error',
            'code' => 400,
            'users' => 'Login failed'
        ]);

        if ($token && $jwtAuth->checkToken($token) == true) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository('BackendBundle:User');
            $users = $userRepo->findAll();

            $result = $helpers->json([
                'status' => 'success',
                'users' => $users
            ]);
        }

        return $result;
    }

    public function loginAction(Request $request) {
        $helpers = $this->get(Helpers::class);

        //RECIBIMOS JSON POR POST
        $json = $request->get('json', null);

        //ARRAY A DEVOLVER POR DEFECTO
        $data = [
            'status' => 'error',
            'data' => 'Send json via post!!'
        ];

        if ($json != null) {

            //CONVERTIMOS UN JSON EN UN OBJETO DE PHP
            $params = json_decode($json);

            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            $getToken = (isset($params->getToken)) ? $params->getToken : null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = 'This email is not valid';
            $validateEmail = $this->get('validator')->validate($email, $emailConstraint);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            if ($email != null && count($validateEmail) == 0 && $password != null) {
                //$jwtAuth = $this->get(JwtAuth::class);
                $jwtAuth = new JwtAuth($em);

                if ($getToken == null || $getToken == false) {
                    //DATOS CODIFICADOS
                    $signUp = $jwtAuth->signUp($email, $password);
                }
                else {
                    //DATOS DECODIFICADOS
                    $signUp = $jwtAuth->signUp($email, $password, true);
                }

                return $this->json($signUp);

                $data = [
                    'status' => 'success',
                    'data' => 'Login correct',
                    'signup' => $signUp
                ];
            }
            else {
                $data = [
                    'status' => 'error',
                    'data' => 'Email or password incorrect'
                ];
            }
        }

        return $helpers->json($data);
    }
}
