<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\FileRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

// NOTE: for this set of endpoints, we provide login and passwords in headers,
// not in json

#[Route('/files', name: 'file')]
class FileController extends AbstractController
{
    #[Route('/', name: '.upload', methods: ["POST"])] 
    public function upload(Request $req, FileUploader $uploader, 
                           UserRepository $user_rep, FileRepository $file_rep): Response
    {
        $login = $req->headers->get("REST-Login");
        $password = $req->headers->get("REST-Password");


        $user = $user_rep->getUser($login, $password);

        if($user)
        {
            $provided_file = $req->files->get('file');
            if($provided_file)
            {
                $file = $file_rep->findOneBy([
                    'originalName' => $provided_file->getClientOriginalName(),
                    'owner' => $user->getId()
                ]);

                if($file)
                {
                    return $this->json([
                        'status' => 400,
                        'message' => "File already uploaded"
                    ]);
                }
                else
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

    #[Route('/', name: '.get_list', methods: ["GET"])] 
    public function getListOfFiles(Request $req, UserRepository $user_rep, 
                                   FileRepository $file_rep): Response
    {
        $login = $req->headers->get("REST-Login");
        $password = $req->headers->get("REST-Password");


        $user = $user_rep->getUser($login, $password);
        if($user)
        {
            $files = $file_rep->findBy([
                "owner" => $user->getId()
            ]);

            $result = [];
            //TODO: size?
            foreach ($files as $file) {
                $array = [
                    "filename" => $file->getOriginalName()
                ];
    
                $result[] = $array;
            }

            return $this->json([
                'status' => 200,
                'files' => $result,
            ]);
        }
        else
        {
            return $this->json([
                'status' => 400,
                'message' => "User not found"
            ]);
        }
    }

    #[Route('/{originalName}', name: '.download', methods: ["GET"])] 
    public function downloadFile(Request $req, UserRepository $user_rep, 
                                 FileRepository $file_rep, $originalName): Response
    {
        $login = $req->headers->get("REST-Login");
        $password = $req->headers->get("REST-Password");

        $user = $user_rep->getUser($login, $password);
        if($user)
        {
            $file = $file_rep->findOneBy([
                "owner" => $user->getId(),
                "originalName" => $originalName
            ]);

            if($file)
            {
                $result_file = $this->getParameter('uploads_dir') . '/' . $file->getSafeName();
                return new BinaryFileResponse($result_file);
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

    #[Route('/{originalName}', name: '.delete', methods: ["DELETE"])]
    public function deleteFile(Request $req, UserRepository $user_rep, 
                               FileRepository $file_rep, $originalName): Response
    {
        $login = $req->headers->get("REST-Login");
        $password = $req->headers->get("REST-Password");

        $user = $user_rep->getUser($login, $password);
        if($user)
        {
            $file = $file_rep->findOneBy([
                "owner" => $user->getId(),
                "originalName" => $originalName
            ]);

            if($file)
            {
                $em = $this->getDoctrine()->getManager();
                $em->remove($file);
                $em->flush();

                $filesystem = new Filesystem();
                try {
                    $filesystem->remove([$this->getParameter('uploads_dir') . '/' . $file->getSafeName()]);
                } catch (IOExceptionInterface $e) {
                    return $this->json([
                        'status' => 400,
                        'message' => 'Exception occured during file deletion',
                        'exception message' => $e->getMessage()
                    ]);
                }

                return $this->json([
                    'status' => 200,
                    'message' => "File succesfully deleted"
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
