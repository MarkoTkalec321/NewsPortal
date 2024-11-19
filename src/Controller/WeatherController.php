<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class WeatherController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private $categoryRepository;

    public function __construct(HttpClientInterface $httpClient, CategoryRepository $categoryRepository)
    {
        $this->httpClient = $httpClient;
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('/weather', name: 'weather_index')]
    public function index(Request $request): Response
    {
        $categories = $this->categoryRepository->findAll();
        $city = $request->query->get('city');

        if (!$city) {
            return $this->render('weather/index.html.twig', [
                'categories' => $categories,
                'city' => $city,
                'weather' => null,
                'error' => 'City is required.',
            ]);
        }

        $apiKey = 'b4daa2af24b7f5752b0c659a18b75d05';
        try {
            $response = $this->httpClient->request('GET', "https://api.openweathermap.org/data/2.5/weather", [
                'query' => [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric',
                ]
            ]);

            $weatherData = $response->toArray();
        } catch (\Exception $e) {
            return $this->render('weather/index.html.twig', [
                'categories' => $categories,
                'city' => $city,
                'weather' => null,
                'error' => 'City not found. Please try again.',
            ]);
        }

        return $this->render('weather/index.html.twig', [
            'categories' => $categories,
            'city' => $city,
            'weather' => $weatherData,
            'error' => null,
        ]);
    }

}