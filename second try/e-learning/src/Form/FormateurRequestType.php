<?php

namespace App\Form;

use App\Entity\FormateurRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormateurRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motivation', TextareaType::class, [
                'label' => 'Motivation',
                'required' => true,
            ])
            ->add('experience', IntegerType::class, [
                'label' => 'Expérience en années',
                'required' => false,
            ])
            ->add('cvPath', FileType::class, [
                'label' => 'Votre CV (PDF)',
                'required' => false,
                'mapped' => false, // if you're not saving the file directly into the entity
            ])
            ->add('submit',SubmitType::class,[]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormateurRequest::class,
        ]);
    }
}
