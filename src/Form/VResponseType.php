<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\VRequest;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VResponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', TextareaType::class, [
                'data' => "Your request has been updated, see response in your email"
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Respond'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VRequest::class,
        ]);
    }
}
