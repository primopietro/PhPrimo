<?php
class PhPrimoModel {
	// Known attributes
	protected $table_name = '';
	protected $primary_key = null; // Not known EVERY time...
	public function addDBObject() {
		$internalAttributes = get_object_vars ( $this );
		
		include $_SERVER ["DOCUMENT_ROOT"] . '/AcierBD/Acier/database_connect.php';
		
		$definition = "INSERT INTO `" . $this->table_name . "`";
		
		echo "table name : " . $this->table_name . "<br>";
		echo "primary_key name : " . $this->primary_key . "<br>";
		
		$attributes = " ( ";
		$values = " VALUES (";
		$lastElement = end ( $internalAttributes );
		foreach ( $internalAttributes as $rowName => $value ) {
			
			if ($rowName != "table_name" && $rowName != "primary_key") {
				
				$attributes .= "`" . $rowName . "`";
				if ($value == null) {
					$values .= "NULL";
				} else {
					$values .= "'" . $value . "'";
				}
				
				if ($value != $lastElement) {
					$attributes .= ",";
					$values .= ",";
				}
			}
		}
		
		$attributes .= " ) ";
		$values .= " ) ";
		
		$sql = $definition . $attributes . $values;
		
		echo $sql;
		if (! $result = $conn->query ( $sql )) {
			// Oh no! The query failed.
			echo "fail";
			exit ();
		} else {
			echo "success";
		}
		
		$conn->close ();
	}
	function updateDBObject() {
		$internalAttributes = get_object_vars ( $this );
		
		$definition = "UPDATE `" . $this->table_name . "` ";
		
		$sets = "SET ";
		
		$lastElement = end ( $internalAttributes );
		foreach ( $internalAttributes as $rowName => $value ) {
			
			if ($value != $this->table_name && $value != $this->primary_key && $rowName != $this->primary_key) {
				
				$sets .= "  `" . $rowName . "` = " . "'" . $value . "'";
				
				if ($value != $lastElement) {
					$sets .= ", ";
				}
			}
		}
		
		$condition = " WHERE  `" . $this->table_name . "`.`" . $this->primary_key . "` = " . $internalAttributes [$this->primary_key];
		
		$sql = $definition . $sets . $condition;
		
		echo "<br>" . $sql;
		
		include $_SERVER ["DOCUMENT_ROOT"] . '/AcierBD/Acier/database_connect.php';
		
		if ($conn->query ( $sql ) === TRUE) {
			echo "success";
		} else {
			echo "fail";
		}
		
		$conn->close ();
	}
	function updateEmployeDynamically($aField, $aValue, $anID) {
		$sql = "UPDATE `" . $this->table_name . "`
		SET `$aField` = '$aValue'
		WHERE `" . $this->table_name . "`.`" . $this->primary_key . "` = '$anID' ";
		
		include $_SERVER ["DOCUMENT_ROOT"] . '/AcierBD/Acier/database_connect.php';
		
		if ($conn->query ( $sql ) === TRUE) {
			echo "success";
		} else {
			echo "fail";
		}
		
		$conn->close ();
	}
	
	// Set to disabled
	function removeDBObject($anID) {
		$sql = "UPDATE `" . $this->table_name . "`
		SET `id_state` = '2'
		WHERE  `" . $this->table_name . "`.`" . $this->primary_key . "` = '$anID' ";
		
		include $_SERVER ["DOCUMENT_ROOT"] . '/AcierBD/Acier/database_connect.php';
		
		if ($conn->query ( $sql ) === TRUE) {
			return "success";
		} else {
			return "fail";
		}
		
		$conn->close ();
	}
	 function getListOfActiveBDObjects() {
		include $_SERVER ["DOCUMENT_ROOT"] . '/AcierBD/Acier/database_connect.php';
		
		$internalAttributes = get_object_vars ( $this );
		
		$sql = "SELECT * FROM `" . $this->table_name . "` WHERE id_state = 1";
		$result = $conn->query ( $sql );
		
		if ($result->num_rows > 0) {
			$fastechObjects = array ();
			while ( $row = $result->fetch_assoc () ) {
				$anObject =  Array();
				$anObject["primary_key"] = $this->primary_key;
				$anObject["table_name"] = $this->table_name;
				foreach ( $row as $aRowName => $aValue ) {
					$anObject[$aRowName] = $aValue;
				}
				
				$fastechObjects [$row [$this->primary_key]] = $anObject;
			}
			
			$conn->close ();
			return $fastechObjects;
		}
		$conn->close ();
		return null;
	}
	function getListOfAllDBObjects() {
		include $_SERVER ["DOCUMENT_ROOT"] . '/AcierBD/Acier/database_connect.php';
		
		$internalAttributes = get_object_vars ( $this );
		
		$sql = "SELECT * FROM `" . $this->table_name . "` ";
		$result = $conn->query ( $sql );
		
		if ($result->num_rows > 0) {
			$fastechObjects = array ();
			while ( $row = $result->fetch_assoc () ) {
				$anObject =  Array();
				$anObject["primary_key"] = $this->primary_key;
				$anObject["table_name"] = $this->table_name;
				foreach ( $row as $aRowName => $aValue ) {
					$anObject[$aRowName] = $aValue;
				}
				
				$fastechObjects [$row [$this->primary_key]] = $anObject;
			}
			
			$conn->close ();
			return $fastechObjects;
		}
		$conn->close ();
		return null;
	}
	function getObjectFromDB($primary_key) {
		include $_SERVER ["DOCUMENT_ROOT"] . '/AcierBD/Acier/database_connect.php';
		
		$internalAttributes = get_object_vars ( $this );
		
		$sql = "SELECT * FROM `" . $this->table_name . "` WHERE " . $this->primary_key . " = $primary_key";
		$result = $conn->query ( $sql );
		
		if ($result->num_rows > 0) {
			$anObject =  Array();
			while ( $row = $result->fetch_assoc () ) {
				$anObject["primary_key"] = $this->primary_key;
				$anObject["table_name"] = $this->table_name;
				foreach ( $row as $aRowName => $aValue ) {
					$anObject[$aRowName] = $aValue;
					$this->$aRowName = $aValue;
				}
			}
			$conn->close ();
			return $anObject;
		}
		$conn->close ();
		return null;
	}
	
	function getObjectAsArrayWithMetadata(){
		return get_object_vars ( $this );
	}
	function getObjectAsArrayWithOutMetadata(){
		$anObject =  get_object_vars ( $this );
		unset($anObject['table_name']);
		unset($anObject['primary_key']);
		return $anObject;
	}
	
	
	//TODO: create intercation with css classes
	function getObjectListAsDynamicTable($cssClasses){
		$aListOfObjects = $this->getListOfActiveBDObjects();
		if ($aListOfObjects != null) {
			foreach ( $aListOfObjects as $anObject ) {
				
				echo "<tr class=''>";
				foreach ( $anObject as $key => $value ) {
					if ($key != "table_name" && $key != "primary_key" && $key != "id_state") {
						$id_object = $anObject ["primary_key"];
						$table_name = $anObject ["table_name"];
						echo "<td><form table='" . $table_name . "' class='edit' iddep='" . $id_object. " '>";
						echo "<input name='" . $key . "' value='" . $value . "'> </form></td>";
					}
				}
				
				echo "</tr>";
			}
		}
	}
	//TODO: create intercation with css classes
	function getObjectListAsStaticTable($cssClasses){
		$aListOfObjects = $this->getListOfActiveBDObjects();
		if ($aListOfObjects != null) {
			foreach ( $aListOfObjects as $anObject ) {
				
				echo "<tr class=''>";
				foreach ( $anObject as $key => $value ) {
					if ($key != "table_name" && $key != "primary_key" && $key != "id_state") {
						$id_object = $anObject ["primary_key"];
						$table_name = $anObject ["table_name"];
						echo "<td>";
						echo  $value . "</td>";
					}
				}
				
				echo "</tr>";
			}
		}
	}
	
}

?>
