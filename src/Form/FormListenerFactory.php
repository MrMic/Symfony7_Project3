<?php

namespace App\Form;

use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Create helper function
 */
class FormListenerFactory
{
    //INFO: CONSTRUCTOR ─────────────────────────────────────────────────────
    public function __construct(private SluggerInterface $slugger)
    {
    }
    // ______________________________________________________________________
    public function autoSlug(string $field): callable
    {
        return function (PreSubmitEvent $event) use ($field) {
            $data = $event->getData();
            if (empty($data['slug'])) {
                $data['slug'] = strtolower($this->slugger->slug($data[$field]));
                $event->setData($data);
            }
        };
    }

    // ______________________________________________________________________
    public function timeStamps(): callable
    {
        return function (PostSubmitEvent $event): void {
            $data = $event->getData();
            $data->setUpdatedAt(new \DateTimeImmutable());
            if (!($data->getId())) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }
}
