# lib_symfony_form
#### lib_symfony_form for Lepton CMS 2 series

This module include the lib_twig module so, if you use this module in your module you do not have to include the lib_twig library file

All the documentation about symfony_form can be see here:

http://symfony.com/fr/doc/current/reference/forms/types.html

http://symfony.com/doc/current/components/form/introduction.html

Each form might have a FormType file.

The example below use the lib_doctrine (cf. https://github.com/loremipsum31/lib_doctrine):

A form for entity group => GroupType see below

------

##### Exemple doctrine entities

```
#modules/articles/Entity/Group.php
namespace Articles\Entity;

/**
 * @ORM\Table(name="lep_mod_articles_groups")
 * @ORM\Entity
 */
class Group
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
```

##### Example Symfony form

```
#modules/articles/Form/GroupType.php

namespace Articles\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GroupType extends AbstractType
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
        return 'post_group';
    }
}
```

##### Example modify.php

```
global $parser, $loader, $formFactory;
if (!isset($formFactory)) {
	//Require de la lib form & twig
	require_once( LEPTON_PATH."/modules/lib_symfony_form/library.php" );
}

$loader->prependPath( dirname(__FILE__)."/templates/backend/", "article" );

$frontend_template_path = LEPTON_PATH."/templates/" . DEFAULT_TEMPLATE . "/backend/article/";
$module_template_path = dirname(__FILE__)."/templates/backend/";

require_once (LEPTON_PATH."/modules/lib_twig/classes/class.twig_utilities.php");
$twig_util = new twig_utilities( $parser, $loader, $module_template_path, $frontend_template_path );
$twig_util->template_namespace = "article";

/** @var $entityManager \Doctrine\ORM\EntityManager */
global $entityManager;
if (!isset($entityManager)) {
	require_once(LEPTON_PATH."/modules/lib_doctrine/library.php");
}

$group = $entityManager->getRepository('Articles\Entity\Group')->find($group_id);
$form  = $formFactory->create(new \Articles\Form\GroupType(), $group);

if (isset($_POST)) {
	$form->handleRequest();
	$fail_url =  sprintf(
		'%s/modules/articles/modify_group.php?page_id=%d&section_id=%d&group_id=%d',
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
	if (true === $twig_util->resolve_path("modify_group.lte") ) {
		echo $parser->render(
			"@news_events/modify_group.lte",
			array(
				'form' 	  => $form->createView(),
				'TEXT'    => $TEXT,
			)
		);
	}
}

```

##### Example modify_group.lte

```
{{ form_start(form) }}
    <input type="hidden" name="group_id" value="{{ form.vars.data.id }}" />
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
