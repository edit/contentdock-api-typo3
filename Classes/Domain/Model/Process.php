<?php
namespace edit\Contentdock\Domain\Model;


class Process extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
	/**
	 * Some agent.
	 *
	 * @var string
	 */
	protected $agent = '';
	
	/**
	 * Some container.
	 *
	 * @var string
	 */
	protected $container = '';
	
	/**
	 * Some command.
	 *
	 * @var string
	 */
	protected $command = '';
	
	/**
	 * Some operation.
	 *
	 * @var string
	 */
	protected $operation = '';
	
	/**
	 * Some data.
	 *
	 * @var string
	 */
	protected $data = '';
	
	/**
	 * Some result.
	 *
	 * @var string
	 */
	protected $result = '';
	
	/**
	 * Some result.
	 *
	 * @var int
	 */
	protected $finished = 0;
	

    /**
	 * An empty constructor - fill it as you like
	 *
	 */
	public function __construct() {
		
	}

	/**
	 * Sets the agent
	 * 
	 * @param string $agent
	 * return void
	 */
	public function setAgent($agent) {
		$this->agent = $agent;
	}
	
	/**
	 * Gets the agent
	 * 
	 * @return string The agent
	 */
	public function getAgent() {
		return $this->agent;
	}

	/**
	 * Sets the container
	 * 
	 * @param string $container
	 * return void
	 */
	public function setContainer($container) {
		$this->container = $container;
	}
	
	/**
	 * Gets the container
	 * 
	 * @return string The container
	 */
	public function getContainer() {
		return $this->container;
	}

	/**
	 * Sets the command
	 * 
	 * @param string $command
	 * return void
	 */
	public function setCommand($command) {
		$this->command = $command;
	}
	
	/**
	 * Gets the command
	 * 
	 * @return string The command
	 */
	public function getCommand() {
		return $this->command;
	}

	/**
	 * Sets the operation
	 * 
	 * @param string $operation
	 * return void
	 */
	public function setOperation($operation) {
		$this->operation = $operation;
	}
	
	/**
	 * Gets the operation
	 * 
	 * @return string The operation
	 */
	public function getOperation() {
		return $this->operation;
	}

	/**
	 * Sets the data
	 * 
	 * @param string $data
	 * return void
	 */
	public function setData($data) {
		$this->data = $data;
	}
	
	/**
	 * Gets the data
	 * 
	 * @return string The data
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Sets the result
	 * 
	 * @param string $result
	 * return void
	 */
	public function setResult($result) {
		$this->result = $result;
	}
	
	/**
	 * Gets the result
	 * 
	 * @return string The result
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * Sets finished
	 * 
	 * @param int $finished
	 * return void
	 */
	public function setFinished($finished) {
		$this->finished = $finished;
	}
	
	/**
	 * Gets finished
	 * 
	 * @return string The finished
	 */
	public function getFinished() {
		return $this->finished;
	}



}

?>