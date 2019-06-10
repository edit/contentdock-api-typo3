<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}


if (TYPO3_MODE === 'BE') {
    
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'edit.contentdock',
        'web',          // Main area
        'contentdock',  // Name of the module
        '',             // Position of the module
        array(               // Allowed controller action combinations
            'Synchroniser' => 'index, sendData, getDataRecords, getOperation, deleteAllRecords, getDataContainer'
        ),
        array(               // Additional configuration
            'access'    => 'user,group',
            'icon'      => 'EXT:contentdock/ext_icon.png',
            'labels'    => 'LLL:EXT:contentdock/Resources/Private/Language/locallang_mod.xml',
        )
    );

}


?>