<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Opinion;

class OpinionRemoveListener
{
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Opinion) {
            $score = $entity->getGivenScore();

            if ($professor = $entity->getProfessor()) {
                $professor->decrementScoreCount($score);
            }

            if ($subject = $entity->getSubject()) {
                $subject->decrementScoreCount($score);
            }
        }
    }
}
