<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;


class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('username', null, [
                'label' => 'Pseudo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas être vide',
                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 12,
                        'minMessage' => "Le pseudo doit faire 4 caractères au minimum",
                        'maxMessage' => "Le pseudo doit faire 12 caractères au maximum"
                    ]),
                ]
            ]);

            $builder->add('email', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas être vide',
                    ]),
                    new Email([
                        'message' => 'L\'email n\'est pas valide'
                    ]),
                ]
            ]);
            $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options'  => array('label' => 'Mot de passe'),
            'second_options' => array('label' => 'Répetez le mot de passe'),
            'invalid_message' => 'Les mots de passe ne correspondent pas',
            'constraints' => [

                    new Length([
                    'min' => '5',
                    'max' => '15',
                    'minMessage' => 'Le mot de passe doit faire 5 caractères minimum',
                    'maxMessage' => 'Le mot de passe doit faire 15 caractères maximum',
                ]),
                ($options['edit'] || $options['edit_admin']) ?

                    new Optional() : new NotBlank([
                    'message' => "Ce champ ne doit pas être vide",
                ]),
            ]
        ]);
        if($options['edit_admin'])
        {
            $builder->add('role');
        }

        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\User',
                'attr' => ['novalidate' => 'novalidate'],
                'edit_admin' => false,
                'edit' => false,
            ));
        }


}
