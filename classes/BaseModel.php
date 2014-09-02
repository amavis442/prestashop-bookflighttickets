<?php
/**
 * @author: Patrick Teunissen <p.teunissen@ict-concept.nl>
 * Date: 12/20/13.
 */

class BaseModel extends ObjectModel
{
	public $tableName;

	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id, $id_lang, $id_shop);
	}

	public function getTable()
	{
		$this->tableName;
	}


	public function find()
	{

	}


	public function findByPk($id)
	{

	}

	public function findAll(CDbCriteria $criteria=null)
	{
		$definition = ObjectModel::getDefinition($this);

		$sql = sprintf('SELECT %s FROM %s%s','*',_DB_PREFIX_,$definition['table']);
		$result = DB::getInstance()->executeS($sql);
		var_dump($result);
	}
}