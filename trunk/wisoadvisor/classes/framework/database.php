<?php
//Database kapselt Funktionen zum Datenbankzugriff. Derzeit wird nur die mysql-Schnittstelle für mySql-Datenbanken unterstützt, eine Erweiterung um andere DB-Typen ist aber möglich.

class Database
{

	//Member-Variablen für die DB-Verbindung
	private $dbType = '';
	private $dbServer = '';
	private $dbDatabase = '';
	private $dbUser = '';
	private $dbPassword = '';
	private $dbError = null;
	private $dbConnection = null; //enthält später das Verbindungsobjekt
	private $dbResult = null;
		
	//Konstruktor: liefert entweder ein Verbindungsobjekt oder false, falls Verbindung misslingt
	public function __construct( $type, $server, $database, $user, $password )
	{
		//setze Member-Variablen
		$this->dbType = strtolower($type);
		$this->dbServer = $server;
		$this->dbDatabase = $database;
		$this->dbUser = $user;
		$this->dbPassword = $password;
		
		return $this->connect();
	}

	//getType gibt den Datenbanktyp zurück
	public function getType()
	{
		return $this->dbType;
	}
	
	//getError() gibt Fehler aus
	public function getError()
	{
		//return $this->dbError;
		switch ($this->getType())
		{
			case 'mysql':
			case 'mysqli':
    			return mysql_error();
			
			default:
				return '';
		}
	}

	//setError() setzt die Fehlervariable
	protected function setError( $error )
	{
		$this->dbError = $error;
		return true;
	}

	//connect baut die DB-Verbindung auf
	public function connect()
	{
		switch ($this->getType())
		{
			case 'mysql':
			case 'mysqli':
    				$this->dbConnection = @mysql_connect($this->dbServer, $this->dbUser, $this->dbPassword);
				if (!$this->dbConnection) $this->setError(mysql_error());
				else $this->dbConnection = mysql_select_db($this->dbDatabase, $this->dbConnection);
				break;
			
			default:
				break;
		}
		
		if ($this->dbConnection) return $this->dbConnection;
		else return false;
	}

	//disconnect beendet die DB-Verbindung
	public function disconnect()
	{
		$returnVal = false;
		switch ($this->getType())
		{
			case 'mysql':
			case 'mysqli':
				$returnVal = mysql_close();
				break;
			
			default:
				break;
		}
		return $returnVal;
	}

	/**
	 * 	query stellt eine Anfrage an die DB und liefert ein Resultset oder true zurück; 
	 *  zusätzlich wird das Result intern im Objekt gespeichert, so dass direkt im Objekt darauf 
	 *  zugegriffen werden kann (z.B. hintereinander Aufruf von query und fetch_array...
	 *  query liefert false, wenn ein Fehler auftrat
	 *  @param $sql ein SQL-Statement
	 */
	public function query( $sql )
	{
		$returnVal = false;
		switch ($this->getType())
		{
			case 'mysql':
			case 'mysqli':
				$returnVal = mysql_query($sql);
				break;
			case 'firebird':
				$returnVal = ibase_query($sql);
				break;
			default:
				break;
		}
		return $returnVal;
	}

	//lastId() gibt die letzte, von der DB automatisch vergebene ID (auto_inc) zurück
	public function lastId()
	{
		$returnVal = false;
		switch ($this->getType())
		{
			case 'mysql':
			case 'mysqli':
				$returnVal = mysql_insert_id();
				break;
			
			default:
				break;
		}
		return $returnVal;
	}

	/**
	 * fetch_array liefert aus einem gegebenen Result den nächsten Datensatz als Array (nummeriert und assoziativ!)
	 * @param $result ein DB-result
	 * @return ein Datensatz als Array
	 **/
	public function fetch_array( $result )
	{
		$returnVal = false;
		switch ($this->getType())
		{
			case 'mysql':
			case 'mysqli':
				$returnVal = mysql_fetch_array($result);
				break;
			case 'firebird':
				$returnVal = ibase_fetch_assoc($result);
				break;
			default:
				break;
		}
		return $returnVal;
	}		

	/*
	 * liefert einen kompletten SQL-String, wobei im String vorkommende ? der Reihe nach
	 * durch die Elemente des Arrays $replacements ersetzt werden
	 * liefert false, falls die Anzahl der Elemente von $replacements nicht der Anzahl der Fragezeichen entspricht
	 * optional kann statt dem Fragezeichen auch ein beliebiger anderer String ersetzt 
	 * werden, wenn $toReplace angegeben wird
	*/
	public function getPreparedStatement($statement, $replacements, $toReplace = '?')
	{
		if ( substr_count($statement, $toReplace) != count($replacements) ) return false;
		else
		{
			$i = 0;
			while ( substr_count($statement, $toReplace) > 0)
			{
				$statement = preg_replace( '/\\'.$toReplace.'/', 
								mysql_real_escape_string($replacements[$i]), 
								$statement, 
								1);		
				$i ++;
			}
			
			return $statement;
		}
	}
	
	/**
	 * preparedQuery() liefert direkt das Abfrageergebnis zu einem "prepared Statement" oder false, wenn es nicht ausgeführt werden konnte
	 * Parameterliste wie getPreparedStatement
	 * @param $statement SQL-Statement als String
	 * @param $replacements Array mit den zu ersetzenden Werten (Reihenfolge ist relevant!)
	 * @param $toReplace (optional) zu ersetzender String (default: ?)
	 * @return ein Resultset oder false
	 */
	public function preparedQuery($statement, $replacements, $toReplace = '?')
	{
		$sql = $this->getPreparedStatement($statement, $replacements, $toReplace);
		
		if (!$sql) return false;
		else return $this->query($sql);	
	}
	
	/**
	 * fetchPreparedRecord() liefert ein assoziatives Array zurück. Es ist verwendbar für Abfragen, die exakt einen Datensatz liefern (z.B. auf PK...)
	 * dazu wird die Methode preparedQuery in Verbindung mit fetchArray() verwendet
	 * @param $statement SQL-Statement als String
	 * @param $replacements Array mit den zu ersetzenden Werten (Reihenfolge ist relevant!)
	 * @param $toReplace (optional) zu ersetzender String (default: ?)
	 * @return ein Recordset
	 */
	public function fetchPreparedRecord($statement, $replacements, $toReplace = '?')
	{
		return $this->fetch_array( $this->preparedQuery($statement, $replacements, $toReplace) );
	}

	/**
	 * fetchRecord() liefert ein assoziatives Array zurück. Es ist verwendbar für Abfragen, die exakt einen Datensatz liefern (z.B. auf PK...)
	 * @param $statement SQL-Statement als String
	 * @return ein Recordset
	 */
	public function fetchRecord($statement)
	{
		return $this->fetch_array( $this->query($statement) );
	}
	
	/**
	 * Liefert die Anzahl der zurückgelieferten Zeilen bei z.B. einen SELECT-Statement.
	 * @param $result Das von query zurückgelieferte Ergebnis
	 * @return int Anzahl der Datensätze
	 */
	public function numRows($result) {
		$returnVal = false;
		switch ($this->getType())
		{
			case 'mysql':
			case 'mysqli':
				$returnVal = mysql_num_rows($result);
				break;
			
			default:
				break;
		}
		return $returnVal;
	}
}

?>