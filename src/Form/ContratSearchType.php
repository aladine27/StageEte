<?php
// src/Form/ContratSearchType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numContrat', TextType::class, [
                'label' => 'Num Contrat',
                'attr' => ['placeholder' => 'Enter Num Contrat'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Set the form action to the URL that will handle the AJAX request to search for contracts.
            'action' => '/search-contracts',
            'method' => 'POST',
        ]);
    }
}
