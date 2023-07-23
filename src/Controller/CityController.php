<?php

namespace App\Controller;

use App\Repository\CountryRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/city')]
class CityController extends AbstractController
{
    #[Route('/index', name: 'app_city_index', methods: ['GET'])]
    public function index(): Response
    {

       exit('running');
    }
}
?>