<?php
// src/Form/ClientType.php
// src/Form/ClientType.php
namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nomprenom', TextType::class)
            ->add('Adresse', TextType::class)
            ->add('Mail', EmailType::class)
            ->add('Motdepasse', PasswordType::class)
            ->add('Tel', TelType::class)
            ->add('Cin', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'S\'inscrire']) ;
            if (!$options['mapped']) {
            $builder->remove('Motdepasse');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
             'mapped' => true, 
        ]);
    }
}
