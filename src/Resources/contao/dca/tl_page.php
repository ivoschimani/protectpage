<?php

use Contao\Config;

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'authRequired';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['authRequired'] = 'auth_user,auth_pw,auth_logo';

$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = str_replace(
    "{publish_legend}",
    "{htaccess_legend},authRequired;{publish_legend}",
    $GLOBALS['TL_DCA']['tl_page']['palettes']['root']
);

$GLOBALS['TL_DCA']['tl_page']['palettes']['rootfallback'] = str_replace(
    "{publish_legend}",
    "{htaccess_legend},authRequired;{publish_legend}",
    $GLOBALS['TL_DCA']['tl_page']['palettes']['rootfallback']
);

$GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] = str_replace(
    "{publish_legend}",
    "{htaccess_legend},authRequired;{publish_legend}",
    $GLOBALS['TL_DCA']['tl_page']['palettes']['regular']
);

$GLOBALS['TL_DCA']['tl_page']['fields']['authRequired'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['authRequired'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true),
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['auth_user'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['auth_user'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => array('maxlength' => 50, 'tl_class' => 'w50', 'mandatory' => true),
    'sql' => "varchar(50) NULL"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['auth_pw'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['auth_pw'],
    'exclude' => true,
    'inputType' => 'password',
    'eval' => array('maxlength' => 255, 'tl_class' => 'w50 clr', 'mandatory' => true),
    'sql' => "varchar(255) NULL"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['auth_logo'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['auth_logo'],
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => array('filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => false, 'tl_class' => 'clr', 'extensions' => Config::get('validImageTypes')),
    'sql' => "binary(16) NULL"
);