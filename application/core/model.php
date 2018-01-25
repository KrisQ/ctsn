<?php

class Model{

	/**
	 * @var object $db_connection The database connection
	 */
	public $db_connection = null;
	/**
	 * @var bool success state of registration
	 */

	public $errors = array();
	/**
	 * @var array collection of success / neutral messages
	 */
	public $messages = array();


	public function __construct(){
		$this->tableName = '`' . $this->tableName . '`';
		if(!isset($this->defaultOrderColumn)){
			$this->defaultOrderColumn = '';
		}
	}

	/**
	 * Checks if database connection is opened and open it if not
	 */
	public function databaseConnection(){
		// connection already opened
		if ($this->db_connection != null) {
			return true;
		} else {
			try {
				$this->db_connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
				return true;
			}
			catch (PDOException $e) {
				$this->errors[] = MESSAGE_DATABASE_ERROR;
				return false;
			}
		}
	}

	public function getColumnNames(){
    $records = array();
    // if database connection opened
    if ($this->databaseConnection()) {
			$select = "DESCRIBE $this->tableName;";
			// $select = 'SELECT * FROM ' . $this->tableName;
			// if($this->defaultOrderColumn){
			// 	$select .= ' ORDER BY ' . $this->defaultOrderColumn;
			// }
      $query = $this->db_connection->prepare($select);
      $query->execute();
      if ($query->rowCount() > 0) {
        while ($record = $query->fetchObject()) {
          $records[] = $record;
        }
      }
    }else{
			throw new Exception('No Db Connection');
		}
    return $records;
  }

	public static function extract($records, $field){
		$ids = array();
		foreach ($records as $key => $record) {
			if (isset($record->$field)) {
				$ids[$record->$field] = $record->$field;
			}
		}
		return array_keys($ids);
	}

	//finds all records
  public function findAllInList($ids, $indexed = false){
    $records = array();
		if (empty($ids)) {
			return $record;
		}
		$ids = implode(',',$ids);
    // if database connection opened
    if ($this->databaseConnection()) {
			$select = 'SELECT * FROM ' . $this->tableName ." WHERE id IN($ids) ORDER BY FIELD(id, $ids)";
      $query = $this->db_connection->prepare($select);
      $query->execute();
      if ($query->rowCount() > 0) {
        while ($record = $query->fetchObject()) {
					if ($indexed && isset($record->id)) {
	          $records[$record->id] = $record;
					}else{
	          $records[] = $record;
					}
        }
      }
    }else{
			throw new Exception('No Db Connection');
		}
    return $records;
  }
	//finds all records
  public function findAll($indexed = false){
    $records = array();
    // if database connection opened
    if ($this->databaseConnection()) {
			$select = 'SELECT * FROM ' . $this->tableName;
			if($this->defaultOrderColumn){
				$select .= ' ORDER BY ' . $this->defaultOrderColumn;
			}
      $query = $this->db_connection->prepare($select);
      $query->execute();
      if ($query->rowCount() > 0) {
        while ($record = $query->fetchObject()) {
					if ($indexed && isset($record->id)) {
						$records[$record->id] = $record;
					}else{
						$records[] = $record;
					}
        }
      }
    }else{
			throw new Exception('No Db Connection');
		}
    return $records;
  }

	var $canDelete = false;
	//delete a specific item by it's primary key
  public function deleteById($id){
		$this->beforeDeleteById($id);
		if ($this->canDelete) {
			$record = false;
			if ($this->databaseConnection()) {
				$select = 'DELETE FROM ' . $this->tableName . ' WHERE id = :id';
				$query = $this->db_connection->prepare($select);
				$query->bindValue(':id', $id, PDO::PARAM_INT );
				$query->execute();
				$record = true;
			}else{
				throw new Exception('No Db Connection');
			}
			return $record;
		}else{
			// Helper::log("cannot delete this model");
			$_SESSION['databaseError'] = "Cannot delete this model.";
			return false;
		}

  }
  //find a specific item by it's primary key
  public function findById($id){
    $record = null;
		if($id){
			if ($this->databaseConnection()) {
				$select = 'SELECT * FROM ' . $this->tableName . ' WHERE id = :id';
				$query = $this->db_connection->prepare($select);
				$query->bindValue(':id', $id, PDO::PARAM_INT );
				$query->execute();
				if ($query->rowCount() > 0) {
					$record = $query->fetchObject();
					$this->afterFind($record);
				}
			}else{
				throw new Exception('No Db Connection');
			}
		}
    return $record;
  }
	public function beforeDeleteById($id){
		return true;
	}
	public function beforeUpdate(&$attributes){
		return true;
	}
	public function afterUpdate(){
		return true;
	}

	public function afterFind(&$data){
		return true;
	}

	public function beforeCreate(&$attributes){
		return true;
	}
	public function afterCreate(){
		return true;
	}

	var $model = null;
	// given an array of attributes and matching values,
	// this funciton creates the model given that the id param is accurate
	// returns true if it succeeded
	public function create($attributes = array()){
		if(!$this->beforeCreate($attributes)){
			return;
		}
		if(empty($attributes)){
			return false;
		}

		$otherCols = $this->getColumnNames();
		foreach ($this->getColumnNames() as $key => $col) {
			if ($col->Field == 'created_by') {
				if (isset($_SESSION['user']->id)) {
					$attributes['created_by'] = $_SESSION['user']->id;
				}
			}
		}
		// create the where statement by mapping the attributes
		// a format like id = :id, name = :name
		$names = array_keys($attributes);
		$columns = implode(', ', $names);
		$values = array();
		foreach($names as $name){
			$values[] = ':' . $name;
		}
		$values = implode(', ', $values);
		$insert = 'INSERT INTO ' . $this->tableName . " ($columns) VALUES ($values)";
		// return;
		// if database connection opened
		if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare($insert);
			try {
				$query->execute($attributes);
			} catch (PDOException $e) {
				Application::log("Error" . $e);
			}
			if ($query->rowCount() > 0) {
				$id = $this->db_connection->lastInsertId();

				$this->model = $this->findById($id);
				$this->afterCreate();
				unset($_SESSION['databaseError']);
				return($this->model);
			}else{

				$_SESSION['databaseError'] = $query->errorInfo()[2];
				// die("bad");
			}
		}else{
			throw new Exception('No Db Connection');
		}
		return false;
	}

	// given an array of attributes and matching values,
  // this funciton updates the model given that the id param is accurate
	// returns true if it succeeded
  public function update($attributes = array()){
    if(empty($attributes)){
      return false;
    }
		$this->beforeUpdate($attributes);
    $set = '';
		$where = 'id = :id';
		$id = $attributes['id'];
    // create the where statement by mapping the attributes
    // a format like id = :id, name = :name
		$otherCols = $this->getColumnNames();
		foreach ($this->getColumnNames() as $key => $col) {
			if ($col->Field == 'updated_by') {
				if (isset($_SESSION['user']->id)) {
					$attributes['updated_by'] = $_SESSION['user']->id;
				}
			}
		}
		$sets = array();
    foreach(array_keys($attributes) as $name){
			if($name != 'id'){
				$set = "$name = :$name";
				$sets[] = $set;
			}
		  $attributes[":$name"] = $attributes[$name];
      unset($attributes[$name]);
    }
		$set = implode($sets,', ');
    $update = 'UPDATE ' . $this->tableName . ' SET ' . $set;
    $update .= ' WHERE '. $where;
		// if database connection opened
    if ($this->databaseConnection()) {
      $query = $this->db_connection->prepare($update);
      $query->execute($attributes);
			return true;
    }else{
      throw new Exception('No Db Connection');
    }
    return false;
  }

  // given an array of attributes and matching values,
  // return an array of those items in the db.
  public function findByAttributes($attributes = array()){
    $record = null;
    if(empty($attributes)){
      return null;
    }
    $where = '';
    // create the where statement by mapping the attributes
    // a format like id = :id, name = :name
    foreach(array_keys($attributes) as $name){
      $where .= "$name = :$name AND ";
      $attributes[":$name"] = $attributes[$name];
      unset($attributes[$name]);
    }
    $where .= '1';
    $select = 'SELECT * FROM ' . $this->tableName;
    $select .= ' WHERE '. $where;
		// Helper::log($select);
		// Helper::log($attributes);
    // if database connection opened
    if ($this->databaseConnection()) {
      $query = $this->db_connection->prepare($select);
      $query->execute($attributes);
      if ($query->rowCount() > 0) {
        $record = $query->fetchObject();
      }
    }else{
      throw new Exception('No Db Connection');
    }
    return $record;
  }
	// given an array of attributes and matching values,
	// return an array of those items in the db.
	public function deleteAllByAttributes($attributes = array()){
		$where = '';
		// create the where statement by mapping the attributes
		// a format like id = :id, name = :name
		foreach(array_keys($attributes) as $name){
			$where .= "$name = :$name AND ";
			$attributes[":$name"] = $attributes[$name];
			unset($attributes[$name]);
		}
		$where .= '1';
		$select = 'DELETE FROM ' . $this->tableName;
		$select .= ' WHERE '. $where;
		// if database connection opened
		if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare($select);
			$query->execute($attributes);
			if ($query->rowCount() > 0) {
				while ($record = $query->fetchObject()) {
					$records[] = $record;
				}
			}
		}else{
			throw new Exception('No Db Connection');
		}
		return $records;
	}
  // given an array of attributes and matching values,
  // return an array of those items in the db.
  public function findAllByAttributes($attributes = array(), $sort = array()){
    $records = array();
    if(empty($attributes)){
      return $this->findAll();
    }
    $where = '';
    // create the where statement by mapping the attributes
    // a format like id = :id, name = :name
    foreach(array_keys($attributes) as $name){
      $where .= "$name = :$name AND ";
      $attributes[":$name"] = $attributes[$name];
      unset($attributes[$name]);
    }
    $where .= '1';
    $select = 'SELECT * FROM ' . $this->tableName;
    $select .= ' WHERE '. $where;
		if(!empty($sort)){
			if(isset($sort['order'])) {
				$select .= ' ORDER BY ' . $sort['order'] . ' ASC';
			}
		}else if($this->defaultOrderColumn){

				$select .= ' ORDER BY ' . $this->defaultOrderColumn;
		}
    // if database connection opened
    if ($this->databaseConnection()) {
      $query = $this->db_connection->prepare($select);
      $query->execute($attributes);
      if ($query->rowCount() > 0) {
        while ($record = $query->fetchObject()) {
          $records[] = $record;
        }
      }
    }else{
      throw new Exception('No Db Connection');
    }
    return $records;
  }
	public function findAllIndexed(){
    $records = array();
    // if database connection opened
    if ($this->databaseConnection()) {
      $select = 'SELECT * FROM ' . $this->tableName;
      if($this->defaultOrderColumn){
        $select .= ' ORDER BY ' . $this->defaultOrderColumn;
      }
      $query = $this->db_connection->prepare($select);
      $query->execute();
      if ($query->rowCount() > 0) {
        while ($record = $query->fetchObject()) {
					if (isset($record->id)) {
						$records[$record->id] = $record;
					}else{
						$records[] = $record;
					}
        }
      }
    }else{
      throw new Exception('No Db Connection');
    }
    return $records;
  }
	public static function fetchIdsFromArray($data){
		$ids = array();
		foreach ($data as $key => $d) {
			if (isset($d->id)) {
				$ids[] = $d->id;
			}
		}
		return $ids;
	}
	public function findAllNotInList($ids = array(), $sort = array()){
		$records = array();
		if(empty($ids)){
			return $this->findAll();
		}
		$listIds = implode(',',$ids);
		$select = 'SELECT * FROM ' . $this->tableName;
		$select .= " WHERE id NOT IN($listIds)";
		if(!empty($sort)){
			if(isset($sort['order'])) {
				$select .= ' ORDER BY ' . $sort['order'] . ' ASC';
			}
		}else if($this->defaultOrderColumn){
				$select .= ' ORDER BY ' . $this->defaultOrderColumn;
		}
		// if database connection opened
		if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare($select);
			$query->execute();
			if ($query->rowCount() > 0) {
				while ($record = $query->fetchObject()) {
					if (isset($record->id)) {
						$records[$record->id] = $record;
					}else{
						$records[] = $record;
					}
				}
			}
		}else{
			throw new Exception('No Db Connection');
		}
		return $records;
	}
	public function findAllByIdsIndexed($ids = array(), $sort = array()){
		$records = array();
		if(empty($ids)){
			return $this->findAll();
		}
		$listIds = implode(',',$id);
		$select = 'SELECT * FROM ' . $this->tableName;
		$select .= " WHERE id IN($listIds)";
		if(!empty($sort)){
			if(isset($sort['order'])) {
				$select .= ' ORDER BY ' . $sort['order'] . ' ASC';
			}
		}else if($this->defaultOrderColumn){
				$select .= ' ORDER BY ' . $this->defaultOrderColumn;
		}
		// if database connection opened
		if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare($select);
			$query->execute($attributes);
			if ($query->rowCount() > 0) {
				while ($record = $query->fetchObject()) {
					if (isset($record->id)) {
						$records[$record->id] = $record;
					}else{
						$records[] = $record;
					}
				}
			}
		}else{
			throw new Exception('No Db Connection');
		}
		return $records;
	}
	public function findAllByAttributesIndexed($attributes = array(), $sort = array()){
		$records = array();
		if(empty($attributes)){
			return $this->findAll();
		}
		$where = '';
		// create the where statement by mapping the attributes
		// a format like id = :id, name = :name
		foreach(array_keys($attributes) as $name){
			$where .= "$name = :$name AND ";
			$attributes[":$name"] = $attributes[$name];
			unset($attributes[$name]);
		}
		$where .= '1';
		$select = 'SELECT * FROM ' . $this->tableName;
		$select .= ' WHERE '. $where;
		if(!empty($sort)){
			if(isset($sort['order'])) {
				$select .= ' ORDER BY ' . $sort['order'] . ' ASC';
			}
		}else if($this->defaultOrderColumn){

				$select .= ' ORDER BY ' . $this->defaultOrderColumn;
		}
		// if database connection opened
		if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare($select);
			$query->execute($attributes);
			if ($query->rowCount() > 0) {
				while ($record = $query->fetchObject()) {
					if (isset($record->id)) {
						$records[$record->id] = $record;
					}else{
						$records[] = $record;
					}
				}
			}
		}else{
			throw new Exception('No Db Connection');
		}
		return $records;
	}
}
