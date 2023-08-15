<?php

namespace App\Form;

use App\Entity\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Num_Facture')
            ->add('Type_Fact')
            ->add('Date_Lim_Pay')
            ->add('Net_Apayer')
            ->add('Anc_Index')
            ->add('Nouv_Inedx')
            ->add('Estimation')
            ->add('contrat')
            ->add('Etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
