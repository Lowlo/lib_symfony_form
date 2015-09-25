# lib_symfony_form
#### lib_symfony_form for Lepton CMS 2 series

This module require the lib_twig module.

All the documentation about Symfony Form component can be see here:
http://symfony.com/doc/current/components/form/introduction.html

And examples how to use here:
http://symfony.com/fr/doc/current/reference/forms/types.html

The example below use the lib_doctrine (cf. https://github.com/loremipsum31/lib_doctrine):

An entity News with a form NewsType

------

##### Exemple doctrine entities

```php
#modules/news/Entity/News.php
<?php

namespace News\Entity;

/**
 * @ORM\Table(name="lep_mod_news")
 * @ORM\Entity
 */
class News
{
    /**
     * @var \Section
     * @ORM\ManyToOne(targetEntity="\Section", cascade={"persist"})
     * @ORM\JoinColumn(name="section_id", referencedColumnName="section_id", nullable=true)
     */
    protected $section;

    /**
     * @var \Page
     * @ORM\ManyToOne(targetEntity="\Page", cascade={"persist"})
     * @ORM\JoinColumn(name="page_id", referencedColumnName="page_id", nullable=true)
     */
    protected $page;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    protected $active = false;
    
    //Getter and setter must be define
```

##### Example Symfony form

```php
#modules/news/Form/NewsType.php
<?php

namespace News\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'required' => true
            ))
            ->add('active', 'checkbox', array(
                'required' => false,
            ))
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'news_form';
    }
}
```

##### Example modify_news.php

```php
global $parser, $loader, $formFactory;
<?php

if (!isset($formFactory)) {
	//Require de la lib form & twig
	require_once( LEPTON_PATH."/modules/lib_symfony_form/library.php" );
}

$loader->prependPath( dirname(__FILE__)."/templates/backend/", "news" );

$frontend_template_path = LEPTON_PATH."/templates/" . DEFAULT_TEMPLATE . "/backend/news/";
$module_template_path = dirname(__FILE__)."/templates/backend/";

require_once (LEPTON_PATH."/modules/lib_twig/classes/class.twig_utilities.php");
$twig_util = new twig_utilities( $parser, $loader, $module_template_path, $frontend_template_path );
$twig_util->template_namespace = "news";

/** @var $entityManager \Doctrine\ORM\EntityManager */
global $entityManager;
if (!isset($entityManager)) {
	require_once(LEPTON_PATH."/modules/lib_doctrine/library.php");
}

$group = $entityManager->getRepository('News\Entity\News')->find($group_id);
$form  = $formFactory->create(new \News\Form\NewsType(), $group);

if (isset($_POST)) {
	$form->handleRequest();
	$fail_url =  sprintf(
		'%s/modules/articles/modify_news.php?page_id=%d&section_id=%d&group_id=%d',
		WB_URL, $page_id, $section_id, $group->getId()
	)
	if($form->isValid()){
		try{
			$entityManager->persist($group);
			$entityManager->flush();
			$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
		}catch (\Exception $e) {
			$admin->print_error($e->getMessage(), $fail_url);
		}
	} else {
		$admin->print_error($MESSAGE['GENERIC']['FILL_IN_ALL'], $fail_url);
	}
} else {
	if (true === $twig_util->resolve_path("modify_news.lte") ) {
		echo $parser->render(
			"@news/modify_news.lte",
			array(
				'form' 	  => $form->createView(),
				'TEXT'    => $TEXT,
			)
		);
	}
}

```

##### Example modify_news.lte

```twig
{{ form_start(form) }}
    <input type="hidden" name="page_id" value="{{ form.vars.data.page.id }}" />
    <input type="hidden" name="section_id" value="{{ form.vars.data.section.id }}" />

    {{ form_rest(form) }}

    <div class="form-actions">
        <input name="save" type="submit" value="{{ TEXT.SAVE }}" class="btn btn-primary" />
        <input class="reset btn btn-danger" type="button" value="{{ TEXT.CANCEL }}"
               onclick="javascript: window.location='{{ ADMIN_URL }}/pages/modify.php?page_id={{ PAGE_ID }}'" />
    </div>

{{ form_end(form) }}
```
