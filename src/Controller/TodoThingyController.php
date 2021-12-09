<?php

namespace App\Controller;

use App\Entity\TodoThingy;
use App\Repository\TodoThingyRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo', name: 'todo')]
class TodoThingyController extends AbstractController
{
    #[Route('/', name: '.get', methods: ['GET'])]
    public function getTodos(Request $req, UserRepository $user_rep, TodoThingyRepository $todo_rep): Response
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

        $user_credentials_provided = isset($data['login']) && isset($data['password']);
        if (!$user_credentials_provided) {
            return $this->json(
                [
                'status' => 400,
                'message' => 'You should provide both login and password',
                ]
            );
        }

        $user = $user_rep->findOneBy(
            [
            'login' => $data['login'],
            'password' => hash('sha256', $data['password']),
            ]
        );

        if ($user) {
            $todos = $todo_rep->findBy(
                [
                'user' => $user,
                ]
            );

            $result = [];
            //$todos_json = json_encode($todos);
            // NOTE: absolutely SHAMELESSLY(!), DISGUSTINGLY(!!!) taken from ykropchik repo (i hate php and i dont understand what json_encode do)
            // NOTE: also i hate bash like honestly wtf is this jesus
            foreach ($todos as $todo) {
                $array = [
                    'id' => $todo->getId(),
                    'description' => $todo->getDescription(),
                ];

                $result[] = $array;
            }

            return $this->json(
                [
                'status' => 200,
                'todos' => $result,
                ]
            );
        } else {
            return $this->json(
                [
                'status' => 400,
                'message' => 'User not found',
                ]
            );
        }
    }

    #[Route('/', name: '.post', methods: ['POST'])]
    public function postTodo(Request $req, UserRepository $rep): Response
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

        $user_credentials_provided = isset($data['login']) && isset($data['password']);
        if (!$user_credentials_provided) {
            return $this->json(
                [
                'status' => 400,
                'message' => 'You should provide both login and password',
                ]
            );
        }

        $user = $rep->findOneBy(
            [
            'login' => $data['login'],
            'password' => hash('sha256', $data['password']),
            ]
        );

        if ($user) {
            // $user->getTodoThingies();
            $description_provided = isset($data['description']);
            if ($description_provided) {
                $description = $data['description'];
                $todo = new TodoThingy();
                $todo->setDescription($description);
                $todo->setUser($user);

                $user->addTodoThingy($todo);

                $em = $this->getDoctrine()->getManager();
                $em->persist($todo);
                $em->persist($user);
                $em->flush();

                return $this->json(
                    [
                    'status' => 200,
                    'message' => 'Todo thingy successfully added',
                    ]
                );
            } else {
                return $this->json(
                    [
                    'status' => 400,
                    'message' => 'Description for todo not provided',
                    ]
                );
            }
        } else {
            return $this->json(
                [
                'status' => 400,
                'message' => 'User not found',
                ]
            );
        }
    }

    #[Route('/{id}', name: '.delete', methods: ['DELETE'])]
    public function deleteTodo(Request $req, UserRepository $user_rep, TodoThingyRepository $todo_rep, $id): Response
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

        $user_credentials_provided = isset($data['login']) && isset($data['password']);
        if (!$user_credentials_provided) {
            return $this->json(
                [
                'status' => 400,
                'message' => 'You should provide both login and password',
                ]
            );
        }

        $user = $user_rep->findOneBy(
            [
            'login' => $data['login'],
            'password' => hash('sha256', $data['password']),
            ]
        );

        if ($user) {
            $todo = $todo_rep->findOneBy(
                [
                'id' => $id,
                ]
            );

            if ($todo) {
                if ($todo->getUser() == $user) {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($todo);
                    $em->flush();

                    return $this->json(
                        [
                        'status' => 200,
                        'message' => 'Succesfully deleted todo',
                        ]
                    );
                } else {
                    return $this->json(
                        [
                        'status' => 400,
                        'message' => 'You do not have rights to delete this todo',
                        ]
                    );
                }
            } else {
                return $this->json(
                    [
                    'status' => 400,
                    'message' => 'Todo not found',
                    ]
                );
            }
        } else {
            return $this->json(
                [
                'status' => 400,
                'message' => 'User not found',
                ]
            );
        }
    }

    #[Route('/{id}', name: '.update', methods: ['PUT'])]
    public function updateTodo(Request $req, UserRepository $user_rep, TodoThingyRepository $todo_rep, $id): Response
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

        $user_credentials_provided = isset($data['login']) && isset($data['password']);
        if (!$user_credentials_provided) {
            return $this->json(
                [
                'status' => 400,
                'message' => 'You should provide both login and password',
                ]
            );
        }

        $user = $user_rep->findOneBy(
            [
            'login' => $data['login'],
            'password' => hash('sha256', $data['password']),
            ]
        );

        if ($user) {
            $todo = $todo_rep->findOneBy(
                [
                'id' => $id,
                ]
            );

            if ($todo) {
                if ($todo->getUser() == $user) {
                    if (isset($data['description'])) {
                        $em = $this->getDoctrine()->getManager();
                        $todo->setDescription($data['description']);
                        $em->persist($todo);
                        $em->flush();

                        return $this->json(
                            [
                            'status' => 200,
                            'message' => 'Succesfully updated todo',
                            ]
                        );
                    } else {
                        return $this->json(
                            [
                            'status' => 400,
                            'message' => 'Description not provided',
                            ]
                        );
                    }
                } else {
                    return $this->json(
                        [
                        'status' => 400,
                        'message' => 'You do not have rights to update this todo',
                        ]
                    );
                }
            } else {
                return $this->json(
                    [
                    'status' => 400,
                    'message' => 'Todo not found',
                    ]
                );
            }
        } else {
            return $this->json(
                [
                'status' => 400,
                'message' => 'User not found',
                ]
            );
        }
    }
}
