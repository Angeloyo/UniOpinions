<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Opinion;

class OpinionLoadListener
{
    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Opinion) {
            $entity->setOldScore($entity->getGivenScore());
        }
    }
}
