<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\City;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/country')]
class CountryController extends AbstractController
{

    #[Route('/deletecity', name: 'deletecity', methods: ['GET'])]
    public function deletecity(Request $request, EntityManagerInterface $entityManager): Response
    {
       //select * from city where id=
        $city = $entityManager->getRepository(City::class)->find($_GET['id']);
        //select * from city where country_id = 1 limit 1
        $numberofcities = $entityManager->getRepository(City::class)->findBy(['country_id' => $_GET['country']]);
        $numberofcities = count($numberofcities);
        if($numberofcities > 1){
            if ($city) {
                //before delete check if only one city exists or not 
                $entityManager->remove($city);
                $entityManager->flush();
                
                $this->addFlash('success', 'City deleted successfully.');
            } else {
                $this->addFlash('error', 'City not found.');
            }
        }
        else{
            $this->addFlash('error', 'Cant delete only city');
        }
        
       
        return $this->redirectToRoute('app_country_show', ['id' => $_GET['country']]);
        
    }

   
    #[Route('/addcity', name: 'addcity', methods: ['POST'])]
    public function addcity(Request $request, EntityManagerInterface $entityManager)
    {
        $data['city'] = $request->request->get('city');
        $data['country_id'] = $request->request->get('id');
        $city = new City();
    $city->setName($data['city']);
    $city->setCountryId($data['country_id']);

  
    $entityManager->persist($city);

    
    $entityManager->flush();
    $insertedId = $city->getId();
    
        return new Response($insertedId );
    }

   
    
    #[Route('/index', name: 'app_country_index', methods: ['GET'])]
    public function index(CountryRepository $countryRepository): Response
    {
        
        return $this->render('country/index.html.twig', [
            'countries' => $countryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_country_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($country);
            $entityManager->flush();

            return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('country/new.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_country_show', methods: ['GET'])]
    public function show(Country $country, EntityManagerInterface $entityManager): Response
    {
        
       $cities = $entityManager->getRepository(City::class)->findBy(['country_id' => $country->getId()]);
        return $this->render('country/show.html.twig', [
                                                            'country' => $country,
                                                            'cities' => $cities,
                                                        ]
                                                    );
    }

    #[Route('/{id}/edit', name: 'app_country_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Country $country, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('country/edit.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_country_delete', methods: ['POST'])]
    public function delete(Request $request, Country $country, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getId(), $request->request->get('_token'))) {
            $entityManager->remove($country);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
    }

  
    
}
