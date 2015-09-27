<?php

namespace LibSymfonyForm\Form\Extension;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Wysiwyg extends AbstractType
{

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        require_once(WB_PATH . '/modules/' . WYSIWYG_EDITOR . '/include.php');
        ob_start();
        show_wysiwyg_editor("long", "long", $view->vars['value']);
        $view->vars['wysiwyg'] = ob_get_clean();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('mapped', false);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'wysiwyg';
    }
}