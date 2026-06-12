<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre du jeu',
            ])
            ->add('cover', FileType::class, [
                'label' => 'Jaquette de jeu',
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'constraints' => [
                    new Assert\File(
                        maxSize: '1024k',
                        extensions: ['png', 'jpg', 'webp'],
                        extensionsMessage: 'Veuillez entrez une image dans le bon format (png, jpg, webp) !',
                    ),
                ]
            ])
            ->add('developer', null, [
                'label' => 'Developpeur du jeu',
            ])
            ->add('publisher', null, [
                'label' => 'Éditeur du jeu',
            ])
            ->add('release_date', null, [
                'label' => 'Date de sortie',
            ])
            ->add('estimated_playtime', null, [
                'label' => 'Durée de vie du jeu',
            ])
            ->add('description', null, [
                'label' => 'Description',
            ])
            ->add('mode', ChoiceType::class, [
                'label' => 'Mode de jeu',
                'choices' => [
                    'Solo' => 'solo',
                    'Multijoueur' => 'multi',
                    'Solo / Multijoueur' => 'solo_multi',
                    'Jeu en coopérative' => 'coop'
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
