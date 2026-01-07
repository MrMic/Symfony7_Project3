<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class RecipeType extends AbstractType
{
    // ______________________________________________________________________
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_class' => ''
                ])
            ->add('slug', TextType::class, [
                'required' => false,
                // 'constraints' => new Sequentially(
                //     [
                //     new Length(min: 10),
                //     new Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: "Ceci n'est pas un slug valide") ,
                //     ]
                // )
            ])
            ->add('content', TextareaType::class, [
                'empty_class' => ''
                ])
            ->add('duration')
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimeStamps(...))
        ;
    }

    // ______________________________________________________________________
    public function attachTimeStamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();
        if (!($data instanceof Recipe)) {
            return;
        }
        $data->setUpdatedAt(new \DateTimeImmutable());

        if (!($data->getId())) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }

    // ______________________________________________________________________
    public function autoSlug(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        if (empty($data['slug'])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data['title']));
            $event->setData($data);
        }
    }

    // ______________________________________________________________________
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            // 'validation_groups' => ['Default', 'Extra'],
            // 'validation_groups' => ['Default'],
        ]);
    }
}
