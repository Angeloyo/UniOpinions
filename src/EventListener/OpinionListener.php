<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use App\Entity\Opinion;

class OpinionListener
{
    //eliminacion de una opinion
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Opinion) {

            $score = $entity->getGivenScore();
            $keywords = $entity->getKeywords();

            $professor = $entity->getProfessor();
            $subject = $entity->getSubject();

            if ($score !== null){

                //opinion de un profesor
                if ($subject !== null && $professor !== null) {
                    $relationSp = $professor->getRelationWithSubject($subject);

                    if($relationSp){
                        //Decrementar valoracion
                        $relationSp->decrementScoreCount($score);

                        //Decrementar palabras clave
                        $relationSp->decrementKeywordsCount($keywords);
                    }
                    
                }

                //opinion de una asignatura
                if ($subject !== null && $professor === null) {
                    //Decrementar valoracion
                    $subject->decrementScoreCount($score);

                    //Decrementar palabras clave
                    $subject->decrementKeywordsCount($keywords);
                }
            }
            
        }
    }

    //Creacion de una opinion
    public function prePersist(LifecycleEventArgs $args): void
    {

        $entity = $args->getObject();

        if ($entity instanceof Opinion) {

            $entity->setCreationDate(new \DateTime());

            $score = $entity->getGivenScore();
            $keywords = $entity->getKeywords();

            $professor = $entity->getProfessor();
            $subject = $entity->getSubject();

            //opinion de un profesor
            if ($subject !== null && $professor !== null) {

                $relationSp = $professor->getRelationWithSubject($subject);

                if($score !== null){
                    $relationSp->incrementScoreCount($score);
                }

                if($keywords !== null){
                    $relationSp->incrementKeywordsCount($keywords);
                }

            }

            //opinion de una asignatura
            if ($subject !== null && $professor === null) {
                if($score !== null){
                    $subject->incrementScoreCount($score);
                }

                if($keywords !== null){
                    $subject->incrementKeywordsCount($keywords);
                }
            }
        }

    }
    
    //edicion de opinion
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {

            if ($entity instanceof Opinion) {

                $changeset = $uow->getEntityChangeSet($entity);
                
                //si ha cambiado valoracion
                if (isset($changeset['givenScore'])) {
                    $oldScore = $changeset['givenScore'][0];
                    $newScore = $changeset['givenScore'][1];

                    $professor = $entity->getProfessor();
                    $subject = $entity->getSubject();  

                    //opinion de un profesor
                    if ($subject !== null && $professor !== null) {

                        $relationSp = $professor->getRelationWithSubject($subject);

                        if($oldScore !== null){
                            $relationSp->decrementScoreCount($oldScore);
                        }

                        if($newScore !== null){
                            $relationSp->incrementScoreCount($newScore);
                        }
                        
                        // recalculate changes
                        $uow->recomputeSingleEntityChangeSet(
                            $em->getClassMetadata(get_class($relationSp)),
                            $relationSp
                        );

                    } 
                    //opinion de una asignatura
                    elseif ($subject !== null && $professor === null) {

                        if($oldScore !== null){
                            $subject->decrementScoreCount($oldScore);
                        }

                        if($newScore !== null){
                            $subject->incrementScoreCount($newScore);
                        }

                        $uow->recomputeSingleEntityChangeSet(
                            $em->getClassMetadata(get_class($subject)),
                            $subject
                        );
                    }
                }

                // Si han cambiado las palabras clave (keywords)
                if (isset($changeset['keywords'])) {
                    $oldKeywords = $changeset['keywords'][0] ?? []; // Keywords antiguos
                    $newKeywords = $changeset['keywords'][1] ?? []; // Nuevos keywords

                    $professor = $entity->getProfessor();
                    $subject = $entity->getSubject();

                    // Opinión de un profesor
                    if ($subject !== null && $professor !== null) {
                        $relationSp = $professor->getRelationWithSubject($subject);

                        if($relationSp){
                            // Disminuir contador para las palabras clave antiguas que se han eliminado
                            $removedKeywords = array_diff($oldKeywords, $newKeywords);
                            $relationSp->decrementKeywordsCount($removedKeywords);

                            // Incrementar contador para nuevas palabras clave añadidas
                            $addedKeywords = array_diff($newKeywords, $oldKeywords);
                            $relationSp->incrementKeywordsCount($addedKeywords);

                            // Recalcular cambios
                            $uow->recomputeSingleEntityChangeSet(
                                $em->getClassMetadata(get_class($relationSp)),
                                $relationSp
                            );
                        }

                        
                    }
                    // opinion de una asignatura
                    elseif ($subject !== null && $professor === null) {

                        // Disminuir contador para las palabras clave antiguas que se han eliminado
                        $removedKeywords = array_diff($oldKeywords, $newKeywords);
                        $subject->decrementKeywordsCount($removedKeywords);

                        // Incrementar contador para nuevas palabras clave añadidas
                        $addedKeywords = array_diff($newKeywords, $oldKeywords);
                        $subject->incrementKeywordsCount($addedKeywords);

                        // Recalcular cambios
                        $uow->recomputeSingleEntityChangeSet(
                            $em->getClassMetadata(get_class($subject)),
                            $subject
                        );
                    }
                    
                }

            }
        }
    }
}
