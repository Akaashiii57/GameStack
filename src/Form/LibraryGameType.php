<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\GameUser;
use App\Entity\LibraryGame;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class LibraryGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Status du jeu',
                'choices' => [
                    'À faire' => 'À faire',
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                    'Abandonné' => 'Abandonné'
                ],
            ])
            ->add('personalRating', null, [
                'label' => 'Votre note personnelle',
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'max' => 10,
                ],
            ])
            ->add('personalReview', null, [
                'label' => 'Votre retour sur le jeu',
            ])
            ->add('playtime', null, [
                'label' => 'Votre temps de jeu',
            ])
            ->add('startedAt', DateType::class, [
                'label' => 'Commencé à',
                'required' => false,
            ])
            ->add('finishedAt', DateType::class, [
                'label' => 'Finit à ',
                'required' => false,
            ])
            ->add('isFavorite', CheckboxType::class, [
                'label' => 'Est-ce votre jeu favori ?',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LibraryGame::class,
        ]);
    }
}
