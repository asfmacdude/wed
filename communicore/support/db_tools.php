<?php

/*
 * db_tools.php
 *
 * This class will be extended by db_common.php
 * provide a common set of tools for each object.
 *
 */
defined( '_GOOFY' ) or die();

abstract class db_tools
{
	public $rows_affected = 0;
	public $last_id = 0;
	
	private function dbInit()
	{
		global $db_settings;
		
		try
		{
			$connect = new PDO($db_settings['DB_TYPE'].':host='.$db_settings['HOSTNAME'].';dbname='.$db_settings['DB_NAME'],$db_settings['USERNAME'],$db_settings['PASSWORD']);
		}
		catch (PDOException $e)
		{
			$err_message = 'System Error: Database connection failed. ' . $e->getMessage();	
			trigger_error($err_message, E_USER_ERROR);
		}
		
		$connect->query('SET NAMES utf8');
		$connect->num_queries=0;
		
		return $connect;
	}
	
	public function dbQuery($query)
	{
		$connect = $this->dbInit();
		
		try
		{
			$q = $connect->query($query);
		}
		catch (PDOException $e)
		{
			$err_message = 'System Error: Database query failed. ' . $e->getMessage();	
			trigger_error($err_message, E_USER_ERROR);
		}
		
		$connect->num_queries++;
		return $q;
	}
	
	public function dbExecute($sql,$data,$all=false)
	{
		$result              = false;
		$this->rows_affected = 0;
		$this->last_id       = 0;
		$connect             = $this->dbInit();
		$stmt                = $connect->prepare($sql);
		
		if ( ($stmt) && (is_array($data)) )
		{
			foreach ($data as $key=>$value)
			{
				$stmt->bindValue($key,$value);
			}
			
			$records = $stmt->execute();
			
			if ($records)
			{
				if ($all)
				{
					$result = $stmt->fetchALL(PDO::FETCH_ASSOC);
				}
				else
				{
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				}
				
				$this->rows_affected = $stmt->rowCount();
				$this->last_id       = $connect->lastInsertId();				
			}
			else
			{
				dbug($stmt->errorInfo());
			}
		}
		
		return $result;	
	}
	
	public function dbAll($query)
	{
		$q = $this->dbQuery($query);
		
		if (!$q)
		{
			return array();
		}
			
		$results = array();
		
		while($r=$q->fetch(PDO::FETCH_ASSOC))
		{
			$results[] = $r;
		}
		
		return $results;
	}
		
	public function dbRow($query) 
	{
		$q = $this->dbQuery($query);
		return $q->fetch(PDO::FETCH_ASSOC);
	}
	
	public function dbOne($query, $field='id')
	{
		$r = $this->dbRow($query);
		return $r[$field];
	}

	public function dbLastInsertId($id_field)
	{
		// As of 9/13/2013 this function was not working
		$query = 'SELECT LAST_INSERT_ID()';
		return $this->dbQuery($query); 
	}
	
	public function dbAffectedRows()
	{
		return mysql_affected_rows($this->connect);
	}
	
	public function stripslashes_deep($value)
	{
	   $value = is_array($value) ? array_map(array($this,'stripslashes_deep'), $value) : stripslashes($value);
	   return $value;
	}
	
	public function convertValue($type,$value)
	{
		/*
		 * convertValue function
		 *
		 * This function is used primarily with the system_config data table that
		 * is basically a name/value pair table with a type field. This function
		 * will convert the value to whaatever type the type field specifies.
		*/
		if ($type==='string')
		{
			return (string) $value;
		}
		elseif ($type==='bool')
		{
			return (bool) $value;
		}
		elseif ($type==='integer')
		{
			return (integer) $value;
		}
		else
		{
			return $value;
		}
	}
}