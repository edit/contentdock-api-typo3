<?php
namespace edit\Contentdock\Controller;

class SynchroniserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeAction()
    {

    }

	/**
	 * Index action for this controller.
	 *
	 * @return string The rendered view
	 */
	public function indexAction()
	{
        $contentdock = new \edit\Contentdock\Utility\ContentdockUtility();
        $operations = $contentdock->getOperations();

        $this->view->assign('operations', $operations);
	}

    /**
     * Get the contentDock Container structure
     *
     * @return string The rendered view
     */
    public function getDataContainerAction()
    {
        // Create the contentDock Utility
        $contentdock = new \edit\Contentdock\Utility\ContentdockUtility();

        // send to contentDock
        $container = $contentdock->sendApiRequest('', 'getDataContainerTableList');       

        $this->view->assign('container', $container['data']);
    }

    /**
     * Get data records from contentDock
     *
     * @return string The rendered view
     */
    public function getDataRecordsAction()
    {

        // Create the contentDock Utility
        $contentdock = new \edit\Contentdock\Utility\ContentdockUtility();

        // Create Your custom Data
        $myData = new \edit\Contentdock\Utility\MyDataUtility();

        // Build Request Condition
        $condition = $myData->getRecordCondition();

        // Get Data Container ID
        $dataContainerID = $myData->getDataContainerID();

        // Send to contentDock
        $records = $contentdock->getDataContainerTableRecords($condition, $dataContainerID);

        $this->view->assign('records', $records);
        $this->view->assign('containerName', $contentdock->getDataContainerTableName($dataContainerID));
    }

    /**
     * Send data records to contentDock
     *
     * @return string The rendered view
     */
    public function sendDataAction()
	{

        // Create the contentDock Utility
        $contentdock = new \edit\Contentdock\Utility\ContentdockUtility();

        // Create Your custom Data
        $myData = new \edit\Contentdock\Utility\MyDataUtility();

        // Build Request Records
        $requestRecords = $myData->getRequestRecords('Insert-Update');

        // Get T3 Table name for Logging
        $tableT3 = $myData->getT3TableName();

        // Get Data Container ID
        $dataContainerID = $myData->getDataContainerID();

        // Send to contentDock
        $operations = $contentdock->pushToDataContainerTable($requestRecords, $dataContainerID, $tableT3);

        $this->view->assign('operations', $operations);
    }

    /**
     * Delete all records from the contentDock DataContainer
     *
     * @return string The rendered view
     */
    public function deleteAllRecordsAction()
    {
        // Create the contentDock Utility
        $contentdock = new \edit\Contentdock\Utility\ContentdockUtility();

        // Create Your custom Data
        $myData = new \edit\Contentdock\Utility\MyDataUtility();

        // Build Request Records
        $requestRecords = $myData->getRequestRecords('Delete');

        // Get T3 Table name for Logging
        $tableT3 = $myData->getT3TableName();

        // Get Data Container ID
        $dataContainerID = $myData->getDataContainerID();

        // Send to contentDock
        $operations = $contentdock->pushToDataContainerTable($requestRecords, $dataContainerID, $tableT3);

        $this->view->assign('operations', $operations);
    }
    
    /**
     * Get contentDock Operation result
     *
     * @return string The rendered view
     */
    public function getOperationAction()
	{
        if ($this->request->hasArgument('processId')) 
        {
            // Create the contentDock Utility
	        $contentdock = new \edit\Contentdock\Utility\ContentdockUtility();

            // Create YourData
            $myData = new \edit\Contentdock\Utility\MyDataUtility();

			// Get the Operation Results from contentDock
            $operationResults = $contentdock->getOperationResult($this->request->getArgument('processId'));

            $progress = 0;
            $errorMessage = '';

			foreach ($operationResults as $operationResult)
			{
                // Update Reference
                $myData->updateReference($operationResult);

                // Error Code?
                if (!empty($operationResult['errorCode'])) {
                    $errorMessage = $operationResult['errorCode'] . ' ' . $operationResult['errorMessage'];
                }

			}

			$process = $contentdock->getOperationById($this->request->getArgument('processId'));
			if (!empty($process)) {
				$this->view->assign('container', $process->getContainer());
                $this->view->assign('containerName', $contentdock->getDataContainerTableName($process->getContainer()));
				$this->view->assign('command', $process->getCommand());
				$this->view->assign('agent', $process->getAgent());
                $this->view->assign('finished', $process->getFinished());
				$this->view->assign('requestProcess', unserialize($process->getData()) );
				$this->view->assign('resultProcess', unserialize($process->getResult()) );
			}

			$this->view->assign('errorMessage', $errorMessage);
            $this->view->assign('processId', $this->request->getArgument('processId'));
            $this->view->assign('progress', $progress);
		}
	}
}

?>