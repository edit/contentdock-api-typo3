<?php

$GLOBALS['TCA']['tx_contentdock_domain_model_process'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:contentdock/Resources/Private/Language/locallang.xlf:tx_contentdock_domain_model_process',
		'label' => 'pagepath',
		'iconfile' => 'EXT:contentdock/ext_icon.png',
		'hideTable' => 1,
		'rootLevel' => 1,
	),
	'columns' => array(
		'agent' => array(
			'label' => 'LLL:EXT:contentdock/Resources/Private/Language/locallang.xlf:tx_contentdock_domain_model_process.agent',
			'config' => array(
				'type' => 'input',
				'eval' => 'trim',
			)
		),
		'container' => array(
			'label' => 'LLL:EXT:contentdock/Resources/Private/Language/locallang.xlf:tx_contentdock_domain_model_process.container',
			'config' => array(
				'type' => 'input',
				'eval' => 'trim',
			)
		),
		'command' => array(
			'label' => 'LLL:EXT:contentdock/Resources/Private/Language/locallang.xlf:tx_contentdock_domain_model_process.command',
			'config' => array(
				'type' => 'input',
				'eval' => 'trim',
			)
		),
		'operation' => array(
			'label' => 'LLL:EXT:contentdock/Resources/Private/Language/locallang.xlf:tx_contentdock_domain_model_process.operation',
			'config' => array(
				'type' => 'input',
				'eval' => 'trim',
			)
		),
		'data' => array(
			'label' => 'LLL:EXT:contentdock/Resources/Private/Language/locallang.xlf:tx_contentdock_domain_model_process.data',
			'config' => array(
				'type' => 'input',
				'eval' => 'trim',
			)
		),
		'result' => array(
			'label' => 'LLL:EXT:contentdock/Resources/Private/Language/locallang.xlf:tx_contentdock_domain_model_process.result',
			'config' => array(
				'type' => 'input',
				'eval' => 'trim',
			)
		),
		'finished' => array(
			'label' => 'LLL:EXT:contentdock/Resources/Private/Language/locallang.xlf:tx_contentdock_domain_model_process.finished',
			'config' => array(
				'type' => 'input',
				'eval' => 'trim',
			)
		),
	),
	'types' => array(
		0 => array(
			'showitem' => 'agent,container,command,operation,data,result,finished',
		),
	),
);
