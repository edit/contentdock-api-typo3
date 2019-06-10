<?php
namespace edit\Contentdock\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ContentdockUtility
 */
class ContentdockUtility
{

    /**
     * @var contentdockApiKey
     */
    protected $contentdockApiKey;

    /**
     * @var contentdockClientSubdomain
     */
    protected $contentdockClientSubdomain;

    /**
     * @var yourProgramAgent
     */
    protected $yourProgramAgent;

    /**
     * @var contentdockRequestURL
     *
     */
    protected $contentdockRequestURL; 


    /**
     * ProcessRepository
     *
     * @var \edit\Contentdock\Domain\Repository\ProcessRepository
     */
    protected $processRepository = null;

    /**
     * persistenceManager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager = null;


    /**
     * Class Construction
     *
     * @return void
     */
    public function __construct() {
        $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['contentdock']);

        $this->contentdockApiKey = $this->extConf['contentdockApiKey'];
        if (empty($this->contentdockApiKey)) {
          throw new \UnexpectedValueException('The contentDock API Key is not set in the extension manager');
        }

        $this->contentdockClientSubdomain = $this->extConf['contentdockClientSubdomain'];
        if (empty($this->contentdockClientSubdomain)) {
          throw new \UnexpectedValueException('The contentDock client subdomain is not set in the extension manager');
        }

        $this->yourProgramAgent = $this->extConf['programAgent'];
        if (empty($this->yourProgramAgent)) {
          throw new \UnexpectedValueException('The program agent is not set in the extension manager');
        }

        $this->contentdockRequestURL = $this->extConf['contentdockRequestURL'];
        if (empty($this->contentdockRequestURL)) {
          throw new \UnexpectedValueException('The contentDock Request URL is not set in the extension manager');
        }

        $this->processRepository = $this->getObjectManager()->get("edit\\Contentdock\\Domain\\Repository\\ProcessRepository");
        $this->persistenceManager = $this->getObjectManager()->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
    }

    /**
     * Send Request to contentDock
     *
     * @return array
     */
    public function sendApiRequest($data, $command, $dataContainerID = '') 
    {
        if (empty($command))
        {
            return '';
        }

        $result= '';
        $query= '';
        $saveOperation = false;
        switch ($command) 
        {
            case 'getDataContainerTableList':
                $query =  json_encode ( 
                            array ( 
                              "query" => 
                                array (
                                  "command" => "getDataContainerTableList",
                                  "param" => 
                                    array (
                                      $this->contentdockApiKey, 
                                      $this->contentdockClientSubdomain, 
                                      $this->yourProgramAgent 
                                    )
                                )
                            )
                          );


                break;
            case 'getDataContainerTableRecords':
                $query =  json_encode ( 
                            array (
                              "query" => 
                                array (
                                  "command" => "getDataContainerTableRecords",
                                  "param" => 
                                    array (
                                      $this->contentdockApiKey,
                                      $this->contentdockClientSubdomain,
                                      $this->yourProgramAgent,
                                      $dataContainerID,
                                      $data
                                    )
                                )
                            )
                          );
                break;
            case 'pushToDataContainerTable':
                $query =  json_encode ( 
                            array (
                              "query" => 
                                array (
                                  "command" => "pushToDataContainerTable",
                                  "param" => 
                                    array (
                                      $this->contentdockApiKey,
                                      $this->contentdockClientSubdomain,
                                      $this->yourProgramAgent,
                                      $dataContainerID,
                                      $data
                                    )
                                )
                            )
                          );
                $saveOperation = true;

                break;
            case 'getProgressOperationID':
                $query =  json_encode (
                            array(
                              "query" =>
                                array (
                                  "command" => "getProgressOperationID",
                                  "param" => 
                                    array (
                                      $this->contentdockApiKey,
                                      $this->contentdockClientSubdomain,
                                      $this->yourProgramAgent,
                                      $data
                                    )
                                )
                            )
                          );                
                break;
        }

        if (!empty($query))
        {
            $requestData = array ( "data" => $query );
            $requestUrl = $this->contentdockRequestURL;

            $curl = curl_init ( $requestUrl );
            curl_setopt ( $curl, CURLOPT_POST, true );
            curl_setopt ( $curl, CURLOPT_POSTFIELDS, http_build_query ( $requestData ) );
            curl_setopt ( $curl, CURLOPT_USERAGENT,'contentDockAPI/1.0');
            curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );

            $response = curl_exec ( $curl );
            curl_close ( $curl );       

            $response = json_decode ( $response, true );
        } else {
          throw new \UnexpectedValueException('The request to contentDock is empty.');
        }

        $result = $response;

        // check Response has a status
        if ($response['status'] == '1')  
        {
          if ($saveOperation) 
          {
              $result = $this->saveOperation($response, $data, $dataContainerID);
          } 
        } else {
          // check Error code
          if (!empty($response['error'])) 
          {
            switch ( intval ( $response['error'] ) )
              {
                case 1:
                  throw new \UnexpectedValueException('The contentDock API request format is not valid.');
                  break;
                case 2:
                  throw new \UnexpectedValueException('The API contentDock command "' . $command .  '" not exist.');
                  break;
                case 3:
                  throw new \UnexpectedValueException('The contentDock subdomain "' . $this->contentdockClientSubdomain .  '" has not a open API or the API key is not valid.');
                  break;
                case 4:
                  throw new \UnexpectedValueException('The contentDock DataContainer Table ID does not exist.');
                  break;
                case 9:
                  throw new \UnexpectedValueException($response['message']);
                  break;
                default:
                  throw new \UnexpectedValueException('Unexpected contentDock API error.');
              }
          } 
          elseif (!empty($response['errorcode']))
          {
            throw new \UnexpectedValueException($response['message']);
          }
        }
        return $result;
    }


    /**
     * Save Operation from contentDock
     *
     * @return array
     */
    public function saveOperation($response, $requestRecords, $dataContainerID)
    {
        $result = array();
        if (is_array($response)) 
        {
            if (!empty($response['status'])) 
            {
                $result['status'] = $response['status'];

                if ( intval ( $response['status'] ) == 1 )
                {
                    if (!empty($response['processId'])) 
                    {
                        $process = new \edit\Contentdock\Domain\Model\Process();
                        $process->setPid(0);
                        $process->setAgent($this->yourProgramAgent);
                        $process->setContainer($dataContainerID);
                        $process->setCommand('pushToDataContainerTable');
                        $process->setData(serialize($requestRecords));
                        $process->setOperation($response['processId']);

                        $this->processRepository->add($process);
                        $this->persistenceManager->persistAll();   

                        $result['processId'] = $response['processId'];
                    }
                }
            }
        }
        return $result;
    }


    /**
     * Get Operation response from contentDock
     *
     * @return array
     */
    public function getOperationResult($operationID)
    {
 
       $result = array();
 
        if (!empty($operationID)) 
        {
            $data = array($operationID);

            // Send request to contentDock
            $response = $this->sendApiRequest($data, 'getProgressOperationID');
            if ( intval ( $response['status'] ) == 1 )
            {
                $process = $this->getOperationById($operationID);

                if (is_array($response['operationStatus'])) 
                {
                    // Update the Operation response in the local database
                    $process->setResult(serialize($response['operationStatus']));
                    $this->processRepository->update($process);
                    $this->persistenceManager->persistAll(); 

                    // Get Progress
                    $result[] =  array('progress' => $response['operationStatus'][0]['percent'], 'dataContainer' => $process->getContainer());

                    // Update Progress
                    $progress = $response['operationStatus'][0]['percent'];
                    if ($progress == intval(100)) 
                    {
                        // Save Operation as finished
                        $this->saveOperationsFinished($operationID);
                        $process->setFinished(1);
                    }

                    // Get referenceUid and contentdockRecordUid for each processed record
                    foreach ($response['operationStatus'][0]['operations'] as $processedOperation) 
                    {
                        // Error Handling
                        if (!empty($processedOperation['status'])) 
                        {
                          if ($processedOperation['status'] == 'error')
                          {
                            $result[] = array('errorCode' => $processedOperation['errorcode'], 'errorMessage' => $processedOperation['message']);
                          }
                        }

                        // Check Records
                        if (!empty($processedOperation['records'])) {
                          foreach ($processedOperation['records'] as $processedRecord) 
                          {
                              $recordResponse = $processedRecord;

                              $result[] = array('referenceUid' => $recordResponse['referenceUid'], 'contentdockRecordUid' => $recordResponse['contentdockRecordUid'], 'errorcode' => $recordResponse['errorcode'], 'command' => $processedOperation['command'], 'dataContainer' => $process->getContainer(), 'finished' => $process->getFinished() );    
                          }
                        }

                    }
                }  
            }
        }

        return $result;
    }


    /**
     * Load Operation from the local db
     *
     * @return array
     */
    public function getOperationById($operationID)
    {
        $result = '';
        if (!empty($operationID)) 
        {
            $query = $this->processRepository->createQuery();
            $query->matching($query->equals('operation', $operationID));
            $dbResult = $query->execute();
            foreach ($dbResult as $process) 
            {
                if ($process->getOperation() == $operationID) 
                {
                  $result = $process;
                  break;
                }
            }

        }
        return $result;
    }

    /**
     * Load all Operation from the local db
     *
     * @return array
     */
    public function getOperations()
    {
        $query = $this->processRepository->createQuery();
        return $query->execute();
    }

    /**
     * Save Operation as finished
     *
     * @return array
     */
    public function saveOperationsFinished($operationID)
    {
        $process = $this->getOperationById($operationID);
        $process->setFinished(1);
        $this->processRepository->update($process);
        $this->persistenceManager->persistAll(); 
    }

    /**
     * Get DatContainer table name
     *
     * @return array
     */
    public function getDataContainerTableName($tableId)
    {
        $result = '';
        $container = $this->sendApiRequest('', 'getDataContainerTableList');       
        if (is_array($container['data'])) {
          foreach ($container['data'] as $container => $tables) {
            foreach ($tables as $table) {
              if ($table['id'] == $tableId) {
                $result = $container . '->' .$table['title'];
                break;
              }
            }
          }
        }
        return $result;
    }

    /**
     * Push to Data Container
     *
     * @return array
     */
    public function pushToDataContainerTable($requestRecords, $dataContainerID, $tableT3)
    {
        $operation = $this->sendApiRequest($requestRecords, 'pushToDataContainerTable', $dataContainerID);
        $operations[] = array('table' => $tableT3, 'operation' => $operation);

        return $operations;
    }

    /**
     * Get Records from Data Container
     *
     * @return array
     */
    public function getDataContainerTableRecords($condition, $dataContainerID)
    {
        $records = $this->sendApiRequest($condition, 'getDataContainerTableRecords', $dataContainerID);

        return $records;
    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager(): ObjectManager
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }


}
