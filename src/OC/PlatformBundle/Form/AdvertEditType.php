<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 28/02/17
 * Time: 10:34
 */

namespace OC\PlatformBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdvertEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('date');
    }

    public function getParent()
    {
        return AdvertType::class; // TODO: Change the autogenerated stub
    }
}