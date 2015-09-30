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
        $full_name = sprintf('%s[%s]', $form->getParent()->getName(), $form->getName());
        $id = sprintf('%s_%s', $form->getParent()->getName(), $form->getName());
        require_once(WB_PATH . '/modules/' . WYSIWYG_EDITOR . '/include.php');
        ob_start();
        show_wysiwyg_editor($full_name, $id, $view->vars['value']);
        $view->vars['wysiwyg'] = ob_get_clean();
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