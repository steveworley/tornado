<?php
// src/Tornado/SiteBundle/Forms/Type/UploadFile.php
namespace Tornado\SiteBundle\Forms\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SourceCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add fields to the form.
        $builder->add('code', 'textarea', array('label' => null));

        return $builder->getForm();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $defaults = array('data_class' => 'Tornado\ApiBundle\Entity\Resource');
        $resolver->setDefaults($defaults);
    }

    public function getName()
    {
        return 'resource';
    }
}
