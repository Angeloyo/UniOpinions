<?php

namespace App\Controller;

use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;
use App\Repository\SubjectRepository;
use App\Repository\ProfessorRepository;
use App\Repository\OpinionRepository;
use App\Repository\RelationSubjectProfessorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapController extends AbstractController
{
    private $universityRepository;
    private $degreeRepository;
    private $subjectRepository;
    private $professorRepository;
    private $opinionRepository;
    private $relationsSubjectProfessorRepository;

    public function __construct(
        UniversityRepository $universityRepository,
        // DegreeRepository $degreeRepository,
        // SubjectRepository $subjectRepository,
        // ProfessorRepository $professorRepository,
        // OpinionRepository $opinionRepository,
        // RelationSubjectProfessorRepository $relationSubjectProfessorRepository
        )
    {
        $this->universityRepository = $universityRepository;
        // $this->degreeRepository = $degreeRepository;
        // $this->subjectRepository = $subjectRepository;
        // $this->professorRepository = $professorRepository;
        // $this->opinionRepository = $opinionRepository;
        // $this->relationsSubjectProfessorRepository = $relationSubjectProfessorRepository;
    }

    #[Route('/sitemap.xml', name: 'sitemap')]
    public function index()
    {
        $universities = $this->universityRepository->findBy(['accepted' => true]);

        $urls = [];

        $staticUrls = [
            [
                'loc' => $this->generateUrl('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'changefreq' => 'monthly',
                'priority' => '1.0',
            ],
            [
                'loc' => $this->generateUrl('app_about', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'changefreq' => 'monthly',
                'priority' => '0.9',
            ],
            [
                'loc' => $this->generateUrl('app_universities', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
        ];
    
        // Fusionar las URLs estáticas con las dinámicas (si las hay)
        $urls = array_merge($urls ?? [], $staticUrls);
        
        foreach ($universities as $university) {
            // Primero, generamos la URL de la universidad
            $urls[] = [
                'loc' => $this->generateUrl(
                    'app_university',
                    ['universitySlug' => $university->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
            
            // Ahora, obtenemos los grados relacionados con la universidad y generamos sus URLs
            $degrees = $university->getAcceptedDegrees();  // Asegúrate de tener un método que obtenga los grados de la universidad
        
            foreach ($degrees as $degree) {
                $urls[] = [
                    'loc' => $this->generateUrl(
                        'app_degree',
                        [
                            'universitySlug' => $university->getSlug(),
                            'degreeSlug' => $degree->getSlug()
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',  // Puedes ajustar la prioridad según lo que consideres adecuado
                ];

                $subjects = $degree->getAcceptedSubjects();

                foreach ($subjects as $subject) {
                    $urls[] = [
                        'loc' => $this->generateUrl(
                            'app_subject',
                            [
                                'universitySlug' => $university->getSlug(),
                                'degreeSlug' => $degree->getSlug(),
                                'subjectSlug' => $subject->getSlug()
                            ],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        ),
                        'changefreq' => 'weekly',
                        'priority' => '0.5',  // Ajusta la prioridad según lo que consideres adecuado
                    ];

                    // Ahora, obtenemos los profesores relacionados con la asignatura
                    $professors = $subject->getAcceptedProfessors();

                    foreach ($professors as $professor) {
                        $urls[] = [
                            'loc' => $this->generateUrl(
                                'app_professor',
                                [
                                    'universitySlug' => $university->getSlug(),
                                    'degreeSlug' => $degree->getSlug(),
                                    'subjectSlug' => $subject->getSlug(),
                                    'professorSlug' => $professor->getSlug()
                                ],
                                UrlGeneratorInterface::ABSOLUTE_URL
                            ),
                            'changefreq' => 'weekly',
                            'priority' => '0.5',  // Ajusta la prioridad según lo que consideres adecuado
                        ];
                    }
                }
            }
        }
        


        $response = new Response(
            $this->renderView('./sitemap/sitemap.html.twig', ['urls' => $urls]),
            200
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}