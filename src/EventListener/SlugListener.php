<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\String\Slugger\AsciiSlugger;
use App\Entity\University;
use App\Entity\Degree;
use App\Entity\Subject;
use App\Entity\Professor;

class SlugListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof University ||
            $entity instanceof Degree ||
            $entity instanceof Professor ||
            $entity instanceof Subject
        ) {
            $slugger = new AsciiSlugger();
            $entity->setSlug($slugger->slug($entity->getName())->lower());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof University ||
            $entity instanceof Degree ||
            $entity instanceof Professor ||
            $entity instanceof Subject
        ) {
            $slugger = new AsciiSlugger();
            $entity->setSlug($slugger->slug($entity->getName())->lower());
        }
    }
}