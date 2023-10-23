<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ProfessorRepository;
use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AutocompleteController extends AbstractController
{

    private $universityRepository;
    private $degreeRepository;
    private $subjectRepository;
    private $professorRepository;
    private $entityManager;

    public function __construct(
        UniversityRepository $universityRepository,
        DegreeRepository $degreeRepository,
        SubjectRepository $subjectRepository,
        ProfessorRepository $professorRepository,
        EntityManagerInterface $entityManager
        )
    {
        $this->universityRepository = $universityRepository;
        $this->degreeRepository = $degreeRepository;
        $this->subjectRepository = $subjectRepository;
        $this->professorRepository = $professorRepository;
        $this->entityManager = $entityManager;
    }


    #[Route('/autocomplete', name: 'app_autocomplete')]
    public function autocomplete(Request $request)
    {
        $type = $request->query->get('type');
        $term = $request->query->get('term');
        $universityId = $request->query->get('university');
        $degreeId = $request->query->get('degree');
        // $subjectId = $request->query->get('subject');
        $year= $request->query->get('year');

        if ($type === 'university') {
            $universities = $this->universityRepository->findByNameLike($term);

            if($universities){
                $results = array_map(function ($university) {
                    return ['id' => $university->getId(), 'text' => $university->getName()];
                }, $universities);
            }
            else {
                $results = [];
            }
            
        }
        elseif ($type === 'degree') {

            if(is_numeric($universityId) ){
                $university = $this->universityRepository->findOneBy(['id' => $universityId]);

                if ($university) {
                    $degrees = $this->degreeRepository->findByUniversityIdAndNameLike($university->getId(), $term);

                    if($degrees){

                        $results = array_map(function ($degree) {
                            return ['id' => $degree->getId(), 'text' => $degree->getName()];
                        }, $degrees);
                        
                    } else {
                        $results = [];
                    }
                    
                } else {
                    $results = [];
                }
            }else {
                $results = [];
            }
            
        }
        elseif ($type === 'subject') {
            if(is_numeric($universityId) ){
            
                $university = $this->universityRepository->findOneBy(['id' => $universityId]);

                if ($university && is_numeric($degreeId)) {
                    $degree = $this->degreeRepository->findOneByIdAndUniversity($degreeId, $university );
                    
                    if($degree ){

                        if( !empty($year) ){

                            $subjects = $this->subjectRepository->findByDegreeIdAndYearAndNameLike($degree->getId(), $year, $term);

                            if($subjects){

                                $results = array_map(function ($subject) {
                                    return ['id' => $subject->getId(), 'text' => $subject->getName()];
                                }, $subjects);

                            }else {
                                $results = [];
                            }

                        }
                        else{
                            $results = [['id' => 0, 'text' => 'Por favor selecciona primero un año.', 'disabled' => true]];
                        }
                        
                    }
                    

                } else {
                    $results = [];
                }
            }else {
                $results = [];
            }
            
        }
        elseif ($type === 'professor') {

            $professors = $this->professorRepository->findByNameLike($term);

            if($professors){

                //devolver lista ordenada alfabeticamente
                usort($professors, function($professor1, $professor2) {
                    //comparar sin tildes!
                    return strcasecmp(iconv('UTF-8', 'ASCII//TRANSLIT', $professor1->getName()), iconv('UTF-8', 'ASCII//TRANSLIT', $professor2->getName()));
                });

                $results = array_map(function ($professor) {
                    return ['id' => $professor->getId(), 'text' => $professor->getName()];
                }, $professors);

            } else {
                $results = [];
            }
            
        }

        return new JsonResponse($results);
    }

}
