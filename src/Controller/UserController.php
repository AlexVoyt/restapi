<?php

namespace App\Controller;

use App\Entity\User;
use function App\utils\EnsureUserCredentials;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// TODO: correct status codes
// TODO: i really want something like struct UserCredentials and use it throought all project,
// but i really dont want to bother with making new class
// TODO: remove copypasta with checking request for the right format (use somekind of listener?)
#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    public function hashPassword($password)
    {
        return hash('sha256', $password);
    }

    #[Route('/', name: '.create', methods: ['POST'])]
    public function create(Request $req): Response
    {
        $content_type = $req->getContentType();
        if ('json' != $content_type) {
            return $this->json(
                [
                'status' => 400,
                'message' => 'Only application/json content type is allowed',
                ]
            );
        }

        $data = json_decode($req->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return $this->json(
                [
                'status' => 400,
                'message' => 'Error during parsing json',
                ]
            );
        }

        $user_credentials_provided = EnsureUserCredentials($data);
        if (!$user_credentials_provided) {
            return $this->json(
                [
                'status' => 400,
                'message' => 'You should provide both login and password',
                ]
            );
        }

        $login = $data['login'];
        $password = $data['password'];

        $user = new User();
        $user->setLogin($login);
        $user->setPassword(hash('sha256', $password));

        $em = $this->getDoctrine()->getManager();
        try {
            $em->persist($user);
            $em->flush();
        } catch (UniqueConstraintViolationException $exception) {
            return $this->json(
                [
                'status' => 400,
                'message' => 'User with such login already registered',
                ]
            );
        }

        return $this->json(
            [
            'status' => 200,
            'message' => 'success',
            ]
        );
    }
}
