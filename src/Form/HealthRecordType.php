<?php

namespace App\Form;

use App\Entity\HealthRecord;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HealthRecordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vet')
            ->add('pet')
            ->add('examination')
            ->add('startedAt', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('finishedAt', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('comment')
            ->add('status')
            ->add('notified',null,[
                'required'=>false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HealthRecord::class,
            'allow_extra_fields' => true
        ]);
    }
}
