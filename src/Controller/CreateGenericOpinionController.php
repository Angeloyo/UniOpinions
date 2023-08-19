<?php

namespace App\Controller;

use App\Entity\RelationSubjectProfessor;
use App\Repository\ProfessorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;
use App\Repository\SubjectRepository;
use App\Entity\Opinion;
use App\Entity\University;
use App\Entity\Subject;
use App\Entity\Degree;
use App\Entity\Professor;
use Doctrine\ORM\EntityManagerInterface;

class CreateGenericOpinionController extends AbstractController
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

    #[Route('/opinion/new-generic/{universityId?}/{degreeId?}/{subjectId?}', 
    name: 'app_create_generic_opinion')]
    public function createGenericOpinion(
        Request $request,
        ?int $universityId = null,
        ?int $degreeId = null,
        ?int $subjectId = null,
    ): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $session = $request->getSession();
        $referer = $session->get('referer');

        if (!$user->isVerified()) {
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        $university = null;
        $degree = null;
        $subject = null;

        $form = $this->createForm(\App\Form\GenericOpinionFormType::class);

        if($universityId){
            
            $university = $this->universityRepository->find($universityId);
            if (!$university) {
                $this->addFlash('error', 'Universidad no encontrada.');
                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirectToRoute('app_home');
                }
            }

            if($degreeId){

                $degree = $this->degreeRepository->find($degreeId);
                if (!$degree) {
                    $this->addFlash('error', 'Grado no encontrado.');
                    if ($referer) {
                        return $this->redirect($referer);
                    } else {
                        return $this->redirectToRoute('app_home');
                    }
                }
                // Check if the degree is in that university
                if ($degree->getUniversity() !== $university) {
                    $this->addFlash('error', 'El grado especificado no pertenece a la universidad especificada.');
                    if ($referer) {
                        return $this->redirect($referer);
                    } else {
                        return $this->redirectToRoute('app_home');
                    }
                }

                if($subjectId){
                
                    $subject = $this->subjectRepository->find($subjectId);
                    if (!$subject) {
                        $this->addFlash('error', 'Asignatura no encontrada');
                        if ($referer) {
                            return $this->redirect($referer);
                        } else {
                            return $this->redirectToRoute('app_home');
                        }
                    }
                    // Check if the subject is in that degree
                    if ($subject->getDegree() !== $degree) {
                        $this->addFlash('error', 'Asignatura especificada no pertenece al grado especificado');
                        if ($referer) {
                            return $this->redirect($referer);
                        } else {
                            return $this->redirectToRoute('app_home');
                        }
                    }

                    $form->get('year')->setData($subject->getYear());

                }
            }
        }


        $form->handleRequest($request);

        // $errors = $form->getErrors(true);
        // foreach ($errors as $error) {
        //     dump($error->getMessage());
        // }
        
        if ($form->isSubmitted() ) {
            
            $checkUniversity = $form->get('university')->getData();
            $checkDegree = $form->get('degree')->getData();
            $checkSubject = $form->get('subject')->getData();
            $checkYear = $form->get('year')->getData();

            $checkScore = $form->get('givenScore')->getData();
            $checkComment = $form->get('comment')->getData();
            $checkKeywords = $form->get('keywords')->getData();

            $errors = [];

            if (empty($checkUniversity)) {
                $errors['university'] = 'El campo "universidad" es obligatorio';
            }

            if (empty($checkDegree)) {
                $errors['degree'] = 'El campo "grado" es obligatorio';
            }

            if (empty($checkSubject)) {
                $errors['subject'] = 'El campo "asignatura" es obligatorio';
            }

            if (empty($checkYear)) {
                $errors['year'] = 'El campo "año" es obligatorio';
            }

            if (empty($checkScore) && empty($checkComment) && empty($checkKeywords) ) {
                $errors['input'] = 'Debes rellenar al menos uno de los campos: valoración general, comentario, palabras clave';
            }

            if (count($errors) > 0) {
                // Render the form again and pass the errors
                return $this->render('opinion/new_generic.html.twig', [
                    'form' => $form,
                    'selectedUniversity' => $university,
                    'selectedDegree' => $degree,
                    'selectedSubject' => $subject,
                    'selectedYear' => $subject ? $subject->getYear():null,
                    'errors' => $errors,
                ]);

            } else {
                //no hay errores

                $obtainedUniversity = $form->get('university')->getData();
                $obtainedDegree = $form->get('degree')->getData();
                $obtainedSubject = $form->get('subject')->getData();
                $obtainedProfessor = $form->get('professor')->getData();
                $obtainedComment = $form->get('comment')->getData();
                $obtainedScore = $form->get('givenScore')->getData();
                $obtainedKeywords = $form->get('keywords')->getData();
                $obtainedYear = $form->get('year')->getData();

                if(empty($obtainedProfessor)){
                    $obtainedProfessor = null;
                }

                $finalUniversity = null;
                $finalDegree = null;
                $finalSubject = null;
                $finalProfessor = null;
                $rsp = null;

                // si es una universidad que existe
                if( is_numeric($obtainedUniversity)){
                    $finalUniversity = $this->universityRepository->find($obtainedUniversity);
                
                    if($finalUniversity){
                        //quizas exista el grado, quizas no

                        //si existe el grado
                        if(is_numeric($obtainedDegree)){
                            $finalDegree = $this->degreeRepository->find($obtainedDegree);

                            if($finalDegree){
                                //puede que exista asignatura puede que no

                                // si existe asignatura
                                if(is_numeric($obtainedSubject)){
                                    $finalSubject = $this->subjectRepository->find($obtainedSubject);

                                    if($finalSubject){
                                        // puede que exista profesor puede que no
                                    
                                        //primero mirar si no es null
                                        if($obtainedProfessor !== null){

                                            //si existe el profesor
                                            if(is_numeric($obtainedProfessor)){
                                                $finalProfessor = $this->professorRepository->find($obtainedProfessor);
                                            }
                                            // si no existe
                                            else{
                                                //crear el profesor
                                                $finalProfessor = new Professor();
                                                $finalProfessor->setName($obtainedProfessor);
                                            }

                                            //Asignar el profesor (Existente o no) con asignatura
                                            // $finalSubject->addProfessor($finalProfessor);
                                            $rsp = new RelationSubjectProfessor();
                                            // $rsp->setProfessor($finalProfessor);
                                            $finalProfessor->addRelationSubjectProfessor($rsp);
                                            // $rsp->setSubject($finalSubject);
                                            $finalSubject->addRelationSubjectProfessor($rsp);
                                        }
                                    }
                                    
                                }
                                // si no existe asignatura
                                else{
                                    // tampoco existirá profesor¿?¿?
                                    //un profesor puede dar varias asignaturas...

                                    //hay que crear asignatura
                                    $finalSubject = new Subject();
                                    $finalSubject->setName($obtainedSubject);
                                    $finalSubject->setYear($obtainedYear);

                                    // si el profesor no es null hay que crearlo 
                                    if($obtainedProfessor !== null){
                                        
                                        if(is_numeric($obtainedProfessor)){
                                            $finalProfessor = $this->professorRepository->find($obtainedProfessor);
                                        }
                                        else{
                                            $finalProfessor = new Professor();
                                            $finalProfessor->setName($obtainedProfessor);
                                        }
                                       
                                        //asignarlo con la asignatura
                                        // $finalSubject->addProfessor($finalProfessor);
                                        $rsp = new RelationSubjectProfessor();
                                        $finalProfessor->addRelationSubjectProfessor($rsp);
                                        $finalSubject->addRelationSubjectProfessor($rsp);

                                    }
                                    
                                }

                                // se enlaza la asignatura (existente o no) con el grado
                                $finalDegree->addSubject($finalSubject);
                            }
                            
                        }
                        //si no existe el grado
                        else{
                            // tampoco existira asignatura, profesor

                            // hay que crear el grado
                            $finalDegree = new Degree();
                            $finalDegree->setName($obtainedDegree);

                            //hay que crear asignatura 
                            $finalSubject = new Subject();

                            //sin embargo es posible que estuviese preseleccionada
                            if(is_numeric($obtainedSubject)){
                                $finalSubject->setName($this->subjectRepository->find($obtainedSubject)->getName());
                            }
                            else{
                                $finalSubject->setName($obtainedSubject);
                            }

                            $finalSubject->setYear($obtainedYear);

                            //Asignar asignatura con el grado
                            $finalDegree->addSubject($finalSubject);

                            //ver si se ha introducido profesor
                            if($obtainedProfessor !== null){

                                if(is_numeric($obtainedProfessor)){

                                    $finalProfessor = $this->professorRepository->find($obtainedProfessor);

                                }
                                else{
                                    //crear el profesor
                                    $finalProfessor = new Professor();
                                    $finalProfessor->setName($obtainedProfessor);
                                    
                                }
                                //Asignarlo con la asignatura
                                // $finalSubject->addProfessor($finalProfessor);
                                $rsp = new RelationSubjectProfessor();
                                $finalProfessor->addRelationSubjectProfessor($rsp);
                                $finalSubject->addRelationSubjectProfessor($rsp);
                                
                            }
                        }
                        
                        // se asocia el grado (Existente o no) con la universidad
                        $finalUniversity->addDegree($finalDegree);
                        // $this->entityManager->persist($finalDegree);
                    }
                    
                }
                // si es una universidad que NO existe
                else{
                    // crear la universidad
                    $finalUniversity = new University();
                    $finalUniversity->setName($obtainedUniversity);

                    //el grado, asignatura, profesor tampoco existirán

                    // crear grado asignarlo con universidad
                    $finalDegree = new Degree();

                    // es posible que se hayan preseleccionado entidades 
                    //y creado una entidad superior
                    if(is_numeric($obtainedDegree)){
                        $finalDegree->setName($this->degreeRepository->find($obtainedDegree)->getName());
                    }
                    else{
                        $finalDegree->setName($obtainedDegree);
                    }

                    $finalUniversity->addDegree($finalDegree);
                    // $this->entityManager->persist($finalDegree);

                    // crear asignatura asignarla con grado
                    $finalSubject = new Subject();

                    if(is_numeric($obtainedSubject)){
                        $finalSubject->setName($this->subjectRepository->find($obtainedSubject)->getName());
                    }
                    else{
                        $finalSubject->setName($obtainedSubject);
                    }
                    
                    $finalSubject->setYear($obtainedYear);
                    $finalDegree->addSubject($finalSubject);
                    

                    // si profesor no es null, crear profesor y asignarlo con asignatura
                    if($obtainedProfessor !== null){

                        $finalProfessor = new Professor();
                        $finalProfessor->setName($obtainedProfessor);
                        // $finalSubject->addProfessor($finalProfessor);
                        $rsp = new RelationSubjectProfessor();
                        $finalProfessor->addRelationSubjectProfessor($rsp);
                        $finalSubject->addRelationSubjectProfessor($rsp);

                    }
                }

                // comprobar que el usuario no tenga una opinion existente de ese profesor/asignatura
                // opinion de asignatura
                $userId = $user->getId();
                if($finalProfessor === null){
                    $existingOpinion = $finalSubject->getOpinions()->filter(function($opinion) use ($userId) {
                        return $opinion->getOwner()->getId() == $userId && $opinion->getProfessor() === null;
                    })->first();
                    
                    if ($existingOpinion) {
                        $this->addFlash('error', 'Ya tienes una opinión sobre eso.');
                        if ($referer) {
                            return $this->redirect($referer);
                        } else {
                            return $this->redirectToRoute('app_home');
                        }
                    }
                }
                //opinion de profesor
                else{
                    $existingOpinion = $finalProfessor->getOpinions()->filter(function($opinion) use ($userId, $finalSubject) {
                        return $opinion->getOwner()->getId() == $userId && $opinion->getSubject() === $finalSubject;
                    })->first();
        
                    if ($existingOpinion) {
                        $this->addFlash('error', 'Ya tienes una opinión sobre eso.');
                        if ($referer) {
                            return $this->redirect($referer);
                        } else {
                            return $this->redirectToRoute('app_home');
                        }
                    }
                }


                $opinion = new Opinion();
                $opinion->setOwner($user);

                // si no hay profesor la opinion sera de una asignatura
                if($finalProfessor == null){
                    $opinion->setSubject($finalSubject);
                }
                else{
                    // si hay profesor la opinion sera de ese profesor
                    $opinion->setProfessor($finalProfessor);
                    // en una asignatura especifica
                    $opinion->setSubject($finalSubject);
                }

                //agregar a la opinion el comentario y el score
                $opinion->setComment($obtainedComment);
                $opinion->setGivenScore($obtainedScore);
                $opinion->setKeywords($obtainedKeywords);

                //persistir todo
                if($obtainedProfessor !== null){
                    $this->entityManager->persist($finalProfessor);
                }
                if($rsp !== null){
                    $this->entityManager->persist($rsp);
                }

                $this->entityManager->persist($finalSubject);
                $this->entityManager->persist($finalDegree);

                $this->entityManager->persist($finalUniversity);
                
                $this->entityManager->persist($opinion);
                // $this->entityManager->persist($f);

                $this->entityManager->flush();

                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirectToRoute('app_home');
                }

            }

        }

        return $this->render('opinion/new_generic.html.twig', [
            'form' => $form,
            'selectedUniversity' => $university,
            'selectedDegree' => $degree,
            'selectedSubject' => $subject,
            'selectedYear' => $subject ? $subject->getYear():null,
        ]);
    }

}
