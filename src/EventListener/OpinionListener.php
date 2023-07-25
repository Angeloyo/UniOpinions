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

            if ($score !== null){
                if ($professor = $entity->getProfessor() ) {
                    $professor->decrementScoreCount($score);
                }

                if ($subject = $entity->getSubject()) {
                    $subject->decrementScoreCount($score);
                }
            }
            
        }
    }

    // public function postLoad(LifecycleEventArgs $args): void
    // {
    //     $entity = $args->getObject();

    //     if ($entity instanceof Opinion) {
    //         $entity->setOldScore($entity->getGivenScore());
    //     }
    // }

    public function prePersist(LifecycleEventArgs $args): void
    {

        $entity = $args->getObject();

        if ($entity instanceof Opinion) {

            $score = $entity->getGivenScore();

            if ($professor = $entity->getProfessor()) {
                if($score !== null){
                    $professor->incrementScoreCount($score);
                }
            }

            if ($subject = $entity->getSubject()) {
                if($score !== null){
                    $subject->incrementScoreCount($score);
                }
            }
        }

    }

// public function preUpdate(PreUpdateEventArgs $args): void
// {
//     $entity = $args->getObject();

//     if ($entity instanceof Opinion) {

//         if ($args->hasChangedField('givenScore')) {
//             $oldScore = $args->getOldValue('givenScore');
//             $newScore = $args->getNewValue('givenScore');

//             if ($professor = $entity->getProfessor()) {
//                 $professor->decrementScoreCount($oldScore);
//                 $professor->incrementScoreCount($newScore);
//             }

//             if ($subject = $entity->getSubject()) {
//                 $subject->decrementScoreCount($oldScore);
//                 $subject->incrementScoreCount($newScore);
//             }
//         }
//     }
// }

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

                    if ($professor = $entity->getProfessor()) {

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

                    } elseif ($subject = $entity->getSubject()) {

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
