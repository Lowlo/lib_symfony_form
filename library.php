<?php
// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {
	include(WB_PATH.'/framework/class.secure.php');
} else {
	$root = "../";
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= "../";
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) {
		include($root.'/framework/class.secure.php');
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}
// end include class.secure.php

/** @var \Composer\Autoload\ClassLoader $classLoader */
$classLoader = require LEPTON_PATH . "/modules/lib_symfony_form/vendor/autoload.php";

/** @var \Twig_Environment $parser */
global $parser;
/** @var \Twig_Loader_Filesystem $loader */
global $loader;
/** @var \Doctrine\ORM\EntityManager $loader */
global $entityManager;

if(!isset($loader)){
	require_once LEPTON_PATH . "/modules/lib_twig/library.php";
}

if(!isset($entityManager)){
	require_once LEPTON_PATH . "/modules/lib_doctrine/library.php";
}

require_once(LEPTON_PATH . '/modules/lib_symfony_form/Persistence/ManagerRegistry.php');
require_once(LEPTON_PATH . '/modules/lib_symfony_form/Twig/TranslateExtension.php');
$parser->addExtension(new \LibSymfonyForm\Twig\TranslateExtension());

$vendorDir = LEPTON_PATH . '/modules/lib_symfony_form/vendor';
$defaultFormTheme = 'form_custom.lte';

$loader->addPath($vendorDir . '/symfony/twig-bridge/Resources/views/Form');
$loader->addPath(LEPTON_PATH . '/modules/lib_symfony_form/Resources/views/Form');

$formEngine = new \Symfony\Bridge\Twig\Form\TwigRendererEngine(array($defaultFormTheme));
$formEngine->setEnvironment($parser);

$parser->addExtension(
	new \Symfony\Bridge\Twig\Extension\FormExtension(
		new \Symfony\Bridge\Twig\Form\TwigRenderer($formEngine)
	)
);

//($name, array $connections, array $managers, $defaultConnection, $defaultManager, $proxyInterfaceName)
$managerRegistry = new \LibSymfonyForm\Persistence\ManagerRegistry(
	null,
	array($entityManager->getConnection()),
	array('doctrine.entity_manager'),
	null,
	null,
	'\Doctrine\ORM\Proxy\Proxy'
);
$managerRegistry->setService('doctrine.entity_manager', $entityManager);

$builder = new \Symfony\Component\Form\FormFactoryBuilder();
$builder->addExtension(new \Symfony\Component\Form\Extension\Core\CoreExtension());
$builder->addExtension(new \Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension($managerRegistry));

global $builder;
