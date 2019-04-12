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


class RoleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, [
            'label' => 'Nom',
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
        $builder->add('users');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Role',
            'attr' => ['novalidate' => 'novalidate'],
        ));
    }


}
