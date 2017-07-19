<?php

namespace AppBundle\Controller;

use AppBundle\Services\Helpers;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/user/new", name="user_new")
     */
    public function newAction(Request $request)
    {
        $helpers = $this->get(Helpers::class);
        $json = $request->get('json', null);
        $params = json_decode($json);

        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'User not created'
        ];

        if ($json != null) {
            $createdAt = new \DateTime('now');
            $role = 'user';
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name)) ? $params->name : null;
            $surname = (isset($params->surname)) ? $params->surname : null;
            $password = (isset($params->password)) ? $params->password : null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = 'This email is not valid';
            $validateEmail = $this->get('validator')->validate($email, $emailConstraint);

            if ($email != null && count($validateEmail) == 0 && $password != null && $name != null && $surname != null) {
                $user = new User();
                $user
                    ->setCreatedAt($createdAt)
                    ->setRole($role)
                    ->setEmail($email)
                    ->setName($name)
                    ->setSurname($surname);

                /** @var EntityManager $em */
                $em = $this->getDoctrine()->getManager();

                //COMPROBAMOS SI EXISTE
                $issetUser = $em->getRepository('BackendBundle:User')->findBy([
                    'email' => $email
                ]);
                if (count($issetUser) == 0) {
                    $em->persist($user);
                    $em->flush();

                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'User created',
                        'user' => $user
                    ];
                }
                else {
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'User not created, duplicated'
                    ];
                }
            }
        }

        return $helpers->json($data);
    }

    /**
     * @Route("/user/new", name="user_new")
     */
    public function newAction(Request $request)
    {
        $helpers = $this->get(Helpers::class);
        $json = $request->get('json', null);
        $params = json_decode($json);

        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'User not created'
        ];

        if ($json != null) {
            $createdAt = new \DateTime('now');
            $role = 'user';
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name)) ? $params->name : null;
            $surname = (isset($params->surname)) ? $params->surname : null;
            $password = (isset($params->password)) ? $params->password : null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = 'This email is not valid';
            $validateEmail = $this->get('validator')->validate($email, $emailConstraint);

            if ($email != null && count($validateEmail) == 0 && $password != null && $name != null && $surname != null) {
                $user = new User();
                $user
                    ->setCreatedAt($createdAt)
                    ->setRole($role)
                    ->setEmail($email)
                    ->setName($name)
                    ->setSurname($surname);

                /** @var EntityManager $em */
                $em = $this->getDoctrine()->getManager();

                //COMPROBAMOS SI EXISTE
                $issetUser = $em->getRepository('BackendBundle:User')->findBy([
                    'email' => $email
                ]);
                if (count($issetUser) == 0) {
                    $em->persist($user);
                    $em->flush();

                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'User created',
                        'user' => $user
                    ];
                }
                else {
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'User not created, duplicated'
                    ];
                }
            }
        }

        return $helpers->json($data);
    }
}