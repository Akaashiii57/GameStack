<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Mode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('cover_url')
            ->add('developer')
            ->add('publisher')
            ->add('release_date')
            ->add('estimated_playtime')
            ->add('description')
            ->add('mode', ChoiceType::class, [
                'label' => 'Mode de jeu',
                'choices' => [
                    'Solo' => 'solo',
                    'Multi' => 'multi',
                    'Solo_Multi' => 'solo_multi',
                    'Coop' => 'coop'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
