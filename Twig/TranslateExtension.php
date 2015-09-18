<?php

namespace LibSymfonyForm\Twig;


class TranslateExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('trans', array($this, 'trans')),
            new \Twig_SimpleFilter('transChoice', array($this, 'transChoice')),
        );
    }

    public function trans($string)
    {
        return $string;
    }

    public function transChoice($string)
    {
        return $string;
    }

    public function getName()
    {
        return 'trans';
    }

}