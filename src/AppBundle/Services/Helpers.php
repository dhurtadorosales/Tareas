<?php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class Helpers
{
    public $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function holaMundo()
    {
        return "Hola mundo desde mi servicio";
    }

    //TRANSFORMA A JSON
    public function json($data)
    {
        $normalizers = [new GetSetMethodNormalizer()];
        $encoders = [
            "json" => new JsonEncoder()
        ];
        $serializer = new Serializer($normalizers, $encoders);
        $json = $serializer->serialize($data, 'json');
        $response = new Response();
        $response
            ->setContent($json)
            ->headers->set('Content-Type', 'application/json');

        return $response;
    }
}