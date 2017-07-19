<?php

namespace AppBundle\Controller;

use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;
use BackendBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends Controller
{
    /**
     * @Route("/task/new", name="task_new")
     * @Route("/task/edit/{id}", name="task_edit")
     */
    public function newAction(Request $request, $id = null)
    {
        $helpers = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);
        $token = $request->get('authorization', null);
        $authCheck = $jwtAuth->checkToken($token);

        if ($authCheck) {
            $identity = $jwtAuth->checkToken($token, true);
            $json = $request->get('json', null);

            if ($json != null) {
                $params = json_decode($json);
                $createdAt = new \DateTime('now');
                $updatedAt = new \DateTime('now');

                $userId = ($identity->sub != null) ? $identity->sub : null;
                $title = (isset($params->title)) ? $params->title : null;
                $description = (isset($params->description)) ? $params->description : null;
                $status = (isset($params->status)) ? $params->status : null;

                if ($userId != null && $title != null) {
                    //OBTENCIÓN DEL USUARIO QUE VA A CREAR LA TAREA
                    /** @var EntityManager $em */
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository('BackendBundle:User')->findOneBy([
                        'id' => $userId
                    ]);

                    if ($id == null) {
                        //NUEVA TAREA
                        $task = new Task();
                        $task
                            ->setUser($user)
                            ->setTitle($title)
                            ->setDescription($description)
                            ->setStatus($status)
                            ->setCreatedAt($createdAt)
                            ->setUpdatedAt($updatedAt);

                        $em->persist($task);
                        $em->flush();

                        $data = [
                            'status' => 'success',
                            'code' => 200,
                            'data' => $task
                        ];
                    }
                    else {
                        //BUSCAR LA TAREA CON EL ID
                        $task = $em->getRepository('BackendBundle:Task')->findOneBy([
                            'id' => $id
                        ]);

                        //COMPROBAMOS SI EXISTE EL USUARIO LOGUEADO
                        if (isset($identity->sub) && $identity->sub == $task->getUser()->getId()) {
                            $task
                                ->setTitle($title)
                                ->setDescription($description)
                                ->setStatus($status)
                                ->setUpdatedAt($updatedAt);

                            $em->persist($task);
                            $em->flush();

                            $data = [
                                'status' => 'success',
                                'code' => 200,
                                'data' => $task
                            ];
                        }
                        else {
                            $em->persist($task);
                            $em->flush();

                            $data = [
                                'status' => 'error',
                                'code' => 400,
                                'message' => 'Task updated error, you not owner'
                            ];
                        }
                    }


                }
                else {
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Task not created, validation failed'
                    ];
                }
            }
            else {
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Task not created, params failed'
                ];
            }
        }
        else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Authorization not valid'
            ];
        }

        return $helpers->json($data);
    }
}