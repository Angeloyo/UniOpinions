<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use App\Entity\Opinion;

class OpinionListener
{
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Opinion) {

            $score = $entity->getGivenScore();

            $professor = $entity->getProfessor();
            $subject = $entity->getSubject();

            if ($score !== null){
                if ($subject !== null && $professor !== null) {
                    $professor->decrementScoreCount($score);
                }

                if ($subject !== null && $professor === null) {
                    $subject->decrementScoreCount($score);
                }
            }
            
        }
    }

    public function prePersist(LifecycleEventArgs $args): void
    {

        $entity = $args->getObject();

        if ($entity instanceof Opinion) {

            $entity->setCreationDate(new \DateTime());

            $score = $entity->getGivenScore();

            $professor = $entity->getProfessor();
            $subject = $entity->getSubject();

            if ($subject !== null && $professor !== null) {

                if($score !== null){
                    $professor->incrementScoreCount($score);
                }

            }

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

                    if ($subject !== null && $professor !== null) {

                        if($oldScore !== null){
                            $professor->decrementScoreCount($oldScore);
                        }

                        if($newScore !== null){
                            $professor->incrementScoreCount($newScore);
                        }
                        
                        // recalculate changes
                        $uow->recomputeSingleEntityChangeSet(
                            $em->getClassMetadata(get_class($professor)),
                            $professor
                        );

                    } elseif ($subject !== null && $professor === null) {

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
