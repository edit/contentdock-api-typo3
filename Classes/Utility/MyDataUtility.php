<?php
namespace edit\Contentdock\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class MyDataUtility
 */
class MyDataUtility
{
 
    /**
     * @var t3TableName
     */
    protected $t3TableName = 'tx_the_name_of_your_table';

    /**
     * @var t3TableRepository
     */
    protected $t3TableRepository = 'Your\Extension\Domain\Repository\TableRepository';

    /**
     * @var dataContainerID - You find this ID in the Developer Area in the DataContainer Table definition page
     */
    protected $dataContainerID = 'Your data container ID'; 

    /**
     * @var domainFile - For download files from contentdock
     */
    protected $domainFile = 'https://domain.tld';

    /**
     * persistenceManager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager = null;

    /**
     * configurationManager
     *
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     */
    protected $configurationManager = null;


    /**
     * Class Construction
     *
     * @return void
     */
    public function __construct() 
    {
        $this->persistenceManager = $this->getObjectManager()->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $this->configurationManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');

    }

    /**
     * Get the T3 Table Name
     *
     * @return string
     */
	public function getT3TableName()
	{
		return $this->t3TableName;
	}

    /**
     * Get the T3 Table Repository
     *
     * @return string
     */
	public function getT3TableRepository()
	{
		return $this->t3TableRepository;
	}

    /**
     * Get contentDock Data Container ID
     *
     * @return string
     */
	public function getDataContainerID()
	{
		return $this->dataContainerID;	
	}


    /**
     * Load your T3 Record and prepare for contentDock
     *
     * @return array
     */
	public function getRequestRecords($command) 
	{

        // Data array to send to contentDock
        $requestRecords = array();

        $tableRepository = $this->getObjectManager()->get($this->getT3TableRepository());
        $table = $this->getT3TableName();

        $query = $tableRepository->createQuery();
        $where = 'sys_language_uid = 0';
        $queryStatement = '
                SELECT *
                FROM ' . $table . '
                WHERE
                    ' . $where . BackendUtility::BEenableFields($table);

        $query->statement($queryStatement);
        $records =  $query->execute(); 
        
        $ids = array();

        foreach ($records as $record) 
        {
        	$requestRecords = $this->getRecordData($record, $command, $requestRecords, $ids);
        }

        if (($command == 'Insert-Update') && (!empty($ids)))
        {
        	// Check all records, that are not send but have a contentdeock_id
	        $query = $tableRepository->createQuery();
	        $where = 'contentdock_id not in (' . implode(',', $ids) . ') and contentdock_id > 0';
	        $queryStatement = '
	                SELECT *
	                FROM ' . $table . '
	                WHERE
	                    ' . $where;

	        $query->statement($queryStatement);
	        $records =  $query->execute(); 
	        $ids = array();
	        foreach ($records as $record) 
	        {
				$data = array();
				$contentdockId = $record->getContentdockId();
				$requestRecords[] = $this->getRequestCommand($data, $contentdockId, 'Delete');
	        }
        }

        return $requestRecords;

	}


    /**
     * Set your Record Data, merge T3 Fields to contentDock Data Container
     *
     * @return array
     */
	private function getRecordData($record, $command, $requestRecords, &$ids) 
	{

        // Set the saved contentDock ID from your record. This you have to save for each t3 record.
        $contentdockId = $record->getContentdockId();

        // Create data array and set reference id
        $data = array();
        $data['referenceUid'] = $record->getUid();

        // Add here your code for populate the data array
        // ...

        /** 
         Example for using a RTE Field with individual CSS

        if (!empty($record->geRTEField())) 
        {
            $outputRTE = '';
            $renderAssign = array();
            $renderAssign['html'] = $record->geRTEField(); 
            $outputRTE = $this->getTemplateHtml('Render', 'Html', $renderAssign);
            $outputRTE = str_replace('src="fileadmin/', 'src="https://www.domain.tld/fileadmin/', $outputRTE); // set absolute url for images
            $outputRTE = preg_replace('#<a href="t3.*?>([^>]*)</a>#i', '$1', $outputRTE); // remove internal links
            
            $outputCss = '';
            $renderAssign = array();
            $outputCss = $this->getTemplateHtml('Render', 'Css', $renderAssign);

            $data['YourDataContainerFieldNameRTE'] = $outputCss . $outputRTE;
        }
        
        */

        /**
         Example for using a File Field 
        
        $data['YourDataContainerFieldNameFile'] = $this->getFileStructure($record->getFileField());
        
        */

        if (!empty($contentdockId)) {
            $ids[] = $contentdockId;
        }

        $requestRecords[] = $this->getRequestCommand($data, $contentdockId, $command);

    	return $requestRecords;

	} 



    /**
     * Structure for Files
     *
     * @return array
     */
    private function getFileStructure($fileField)
    {
        $result = '';
        $filename = $fileField->getOriginalResource()->getPublicUrl();
        if (!empty($filename)) 
        {
            $result = array (
                'file' => $this->domainFile . $filename,
                'tags' => array() // Set your Tags here, that you will set in contentdock
            );
        }
        return $result;
    }

    /**
     * Set Condition for getRecords
     *
     * @return array
     */
    public function getRecordCondition()
    {
        // Add your conditions that you need. Read also the API documentation

        return array();
    }

    /**
     * Set Request Command
     *
     * @return array
     */
    private function getRequestCommand($data, $contentdockId, $command)
    {

		$result = array();

		switch ($command) {
			case 'Insert-Update':

			  if (!empty($contentdockId)) 
			  {
			    $contentDockCondition = array();
			    $contentDockCondition['contentdockRecordUid'] = intval($contentdockId);
                // if you need sepacial conditions, read also the API documentation

			    $result = array('command' => 'update', 'language' => 'EN', 'data' => $data, 'conditions' => $contentDockCondition);
			  } else {
			    $result = array('command' => 'insert', 'language' => 'EN', 'data' => $data);
			  }

			  break;

			case 'Delete':

			  $contentDockCondition = array();
			  $contentDockCondition['contentdockRecordUid'] = intval($contentdockId);
			  $result = array('command' => 'delete', 'conditions' => $contentDockCondition);

			  break;
		}

      return $result;
    }


    /**
     * Update Reference ID from contentDock
     *
     * @return array
     */
    public function updateReference($operationResult)
    {
        // Check record result
		if ( (empty($operationResult['errorcode'])) || ($operationResult['errorcode'] == 300) )
		{
            if ($operationResult['finished'])
            {
                if ( (!empty($operationResult['referenceUid'])) || (!empty($operationResult['contentdockRecordUid'])) )
                {
                    $tableRepository = $this->getObjectManager()->get($this->getT3TableRepository());
                    $table = $this->getT3TableName();

		            $query = $tableRepository->createQuery();
		         	if (!empty($operationResult['referenceUid'])) 
		         	{ 
		            	$where = 'uid = ' . intval($operationResult['referenceUid']);
		        	} 
		        	elseif (!empty($operationResult['contentdockRecordUid'])) 
		        	{
		            	$where = 'contentdock_id = ' . intval($operationResult['contentdockRecordUid']);
		        	}
		           
		            $queryStatement = '
		                    SELECT *
		                    FROM ' . $table . '
		                    WHERE
		                        ' . $where;
		            $query->statement($queryStatement);
		            $records =  $query->execute(); 
		            foreach ($records as $record) 
		            {
		                if (($record->getUid() == $operationResult['referenceUid']) || 
		                	($record->getContentdockId() == $operationResult['contentdockRecordUid'])) 
		                {
                            switch ($operationResult['command']) 
                            {
                                 case 'insert':
                                    $record->setContentdockId($operationResult['contentdockRecordUid']);
                                     break;
                                 case 'update':
                                    $record->setContentdockId($operationResult['contentdockRecordUid']);
                                     break;
                                 case 'delete':
                                    $record->setContentdockId(0);
                                     break;
                             } 
		                    $tableRepository->update($record);
		                    $this->persistenceManager->persistAll(); 
		                }
		            }
                }                       
            }
    	} else {
    		// Your own special error handling
            // Read also the API documentation
    	}

    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager(): ObjectManager
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }


    /** 
     * getTemplateHtml
     *
     * @return string
     */
    private function getTemplateHtml($controllerName, $templateName, array $variables = array())
    {
        $tempView = $this->getObjectManager()->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:contentdock/Resources/Private/Templates/');
        $templatePathAndFilename = $templateRootPath . $controllerName . '/' . $templateName . '.html';
        $tempHtml = '';
        if (file_exists($templatePathAndFilename)) {
            $tempView->setTemplatePathAndFilename($templatePathAndFilename);
            $tempView->assignMultiple($variables);
            $tempHtml = $tempView->render();
        }

        return $tempHtml;
    }


}
