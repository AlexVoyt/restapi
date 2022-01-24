<?php                                                                                          
                                                                                               
namespace App\Controller;                                                                      
                                                                                               
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;                              
use Symfony\Component\HttpFoundation\Response;                                                 
use Symfony\Component\Routing\Annotation\Route;                                                
use Symfony\Component\HttpFoundation\BinaryFileResponse;                                       
                                                                                               
#[Route('/homm', name: 'h_o_m_m')]                                                             
class HOMMController extends AbstractController                                                
{                                                                                              
                                                                                               
    #[Route('/creatures', name: 'creatures')]                                                  
    public function creatures(): Response                                                      
    {                                                                                          
        return new BinaryFileResponse('/var/www/restapi_dev/src/Controller/creatures.json');   
    }                                                                                          
                                                                                               
    #[Route('/heroes', name: 'heroes')]                                                        
    public function heroes(): Response                                                         
    {                                                                                          
        return new BinaryFileResponse('/var/www/restapi_dev/src/Controller/heroes.json');      
    }                                                                                          
}                                                                                              