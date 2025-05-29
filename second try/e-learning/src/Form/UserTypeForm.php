<?php
// src/Form/UserType.php
namespace App\Form;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstName')
            ->add('lastName')
        ;

        if ($options['is_admin']) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Formateur' => 'ROLE_FORMATEUR',
                ],
                'multiple' => true,
                'expanded' => true, // checkboxes
                'label' => 'Roles',
            ]);
        }
 
            $builder->add('submit', SubmitType::class ,[
                'label' => 'Save'
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_admin' => false, // default value
        ]);
    }
}
