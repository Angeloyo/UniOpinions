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

            $professor = $entity->getProfessor();
            $subject = $entity->getSubject();

            if ($score !== null){


                //opinion de un profesor
                if ($subject !== null && $professor !== null) {
                    $relationSp = $professor->getRelationWithSubject($subject);
                    $relationSp->decrementScoreCount($score);
                }

                //opinion de una asignatura
                if ($subject !== null && $professor === null) {
                    $subject->decrementScoreCount($score);
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

            $professor = $entity->getProfessor();
            $subject = $entity->getSubject();

            //opinion de un profesor
            if ($subject !== null && $professor !== null) {

                if($score !== null){
                    $relationSp = $professor->getRelationWithSubject($subject);
                    $relationSp->incrementScoreCount($score);
                }

            }

            //opinion de una asignatura
            if ($subject !== null && $professor === null) {
                if($score !== null){
                    $subject->incrementScoreCount($score);
                }
            }
        }

    }
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {

            if ($entity instanceof Opinion) {

                $changeset = $uow->getEntityChangeSet($entity);
                
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
            }
        }
    }
}
