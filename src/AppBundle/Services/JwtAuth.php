<?php

namespace AppBundle\Services;


use Firebase\JWT\JWT;

class JwtAuth
{
    public $manager;
    public $key;

    public function __construct($manager)
    {
        $this->manager = $manager;
        $this->key = 'holaquetalsoylaclavesecreta54545431234456-';
    }

    public function signUp($email, $password, $getToken = null) {
        $user = $this->manager->getRepository('BackendBundle:User')->findOneBy([
            'email' => $email,
            'password' => $password
        ]);

        $signUp = false;
        if (is_object($user)) {
            $signUp = true;
        }

        $data = [
            'status' => 'error',
            'data' => 'Login failed !!'
        ];
        if ($signUp == true) {
            //GENERAR TOKEN JWT
            $token = [
                'sub' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            ];

            //CODIFICAR EL TOKEN
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            if ($getToken == null) {
                $data = $jwt;   //DATOS CODIFICADOS
            }
            else {
                $data = $decoded; //DATOS DECODIFICADOS
            }
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false) {
        $auth = false;
        $result = false;
        $decoded = false;

        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }
        catch (\UnexpectedValueException $e) {
            $auth = false;
        }
        catch (\DomainException $e) {
            $auth = false;
        }

        if (isset($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        }
        else {
            $auth = false;
        }

        $result = $decoded;
        if ($getIdentity == false) {
            $result = $auth;
        }

        return $result;
    }
}