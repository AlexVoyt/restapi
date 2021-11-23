<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// NOTE: for this set of endpoints, we provide login and passwords in headers,
// not in json

#[Route('/files', name: 'file')]
class FileController extends AbstractController
{
    #[Route('/', name: '.upload', methods: ["POST"])] 
    public function upload(Request $req, FileUploader $uploader, UserRepository $user_rep): Response
    {

        $login = $req->headers->get("REST-Login");
        $password = $req->headers->get("REST-Password");

        // TODO: this relies on knowledge of how we determine password, should incapsulate this
        $user = $user_rep->findOneBy([
            "login" => $login,
            "password" => hash("sha256", $password)
        ]);

        if($user)
        {
            $provided_file = $req->files->get('file');
            if($provided_file)
            {
                try
                {
                    $safeFilename = $uploader->upload($provided_file);
                    $file = new File();
                    $file->setSafeName($safeFilename);
                    $file->setOriginalName($provided_file->getClientOriginalName());
                    $file->setOwner($user);

                    $user->addFile($file);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->persist($file);
                    $em->flush();

                    return $this->json([
                        'status' => 200,
                        'message' => "File successfully uploaded"
                    ]);
                }
                catch(Exception $e)
                {
                    return $this->json([
                        'status' => 400,
                        'message' => "Exception occured during file uploading",
                        'exception message' => $e->getMessage()
                    ]);
                }

                
                return $this->json([
                    'status' => 200,
                    'message' => "ok"
                ]);
            }
            else
            {
                return $this->json([
                    'status' => 400,
                    'message' => "File not found"
                ]);
            }

        }
        else
        {
            return $this->json([
                'status' => 400,
                'message' => "User not found"
            ]);
        }
    }
}
