<?php

namespace SuperShoesBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, array(
                'description' => 'A brief article description'
            ))
            ->add('price', MoneyType::class)
            ->add('total_in_shelf', IntegerType::class, array(
                'attr' => array('min' => 0)
            ))
            ->add('total_in_vault', IntegerType::class, array(
                'attr' => array('min' => 0)
            ))
            ->add('store', EntityType::class, array(
                'class' => 'SuperShoesBundle\Entity\Store',
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Save'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SuperShoesBundle\Entity\Article',
        ));
    }
}
