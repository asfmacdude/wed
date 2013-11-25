<?php
/*
 * accountant_table_tools.php
 *
 * This class will be extended by each of the fm objects and
 * provide a common set of tools for each object.
 *
 */

defined( '_GOOFY' ) or die();


class accountant_table_tools extends details
{	
	public function getFields()
	{
		$fields = array();
		
		if (!is_null($this->db_connection))
		{
			$fields = $this->db_connection->FIELDS;
		}
		
		return $fields;
	}
	
	public function getUrl($mode='save',$use_id=true)
	{
		$key = $this->options['KEY'];
		$id  = ($use_id) ? $this->options['ID'] : '' ;
		return sprintf($this->options['URL_FORMAT'],$key,$mode,$id);
	}
	
	public function getTableHTML()
	{
		$html = '';		
		
		
		if ($this->JSON)
		{
			$html .= $this->getTableBodyJson();
		}
		else
		{
			$html .= $this->getTableHead();
			$html .= $this->getTableBodyWrap();
			$html .= $this->getTableFooter();
			$html = $this->wrapTable($html);
		}
		// $html .= $this->getTableBody();
		
		
		return $html; // $this->wrapTable($html);
	}
	
	public function wrapTable($html)
	{
		$open      = sprintf($this->TABLE_TAG, $this->TABLE_ID, $this->TABLE_WIDTH);
		$close     = '</table>';
		return $open . $html . $close;
	}
	
	// *******************************************************************
    // ********  BASIC TABLE FUNCTIONS - HEAD, BODY, FOOT ****************
    // *******************************************************************
	public function getTableHead()
	{
		$html    = '';
		$columns = $this->COLUMNS;
		$line    = $this->LINE_FORMAT;
		
		foreach ($columns as $key=>$value)
		{
			if ($this->checkGadget($value))
			{
				$width = (isset($value['WIDTH'])) ? $value['WIDTH'] : '20%' ;
				$title = (isset($value['TITLE'])) ? $value['TITLE'] : 'Column' ;
				$html .= ($key!='ACTION') ? sprintf($line,$width,$title) : '' ;
			}
		}
		
		$html .= $this->getAddButton();
		
		$html = '<thead>'.LINE1.'<tr>'.LINE1.$html.'</tr>'.LINE1.'</thead>'.LINE2;
		
		return $html;			
	}
	
	public function getTableFooter()
	{
		return null;
	}
	
	public function getTableBodyWrap($guts=null)
	{
		return '<tbody>'.LINE1.$guts.'</tbody>'.LINE1;
	}
	
	public function getTableBody()
	{
		$html    = '';
		$columns = $this->COLUMNS;	
		$data    = $this->getTableData();
		$fields  = $this->getFields();
		
		if (!$data) // in case there is an arror
		{
			return $html;
		}
		
		foreach ($data as $row=>$info)
		{
			// Start <tr>
			$html .= '<tr>'.LINE1;
			
			foreach ($columns as $key=>$value)
			{
				if ($this->checkGadget($value))
				{
					// Establish initial value of the cell. If key = ACTION, then start with a blank cell, FORMAT will take of it's value
					$cell = ($key==='ACTION') ? '' : $info[$fields[$key]['DB_FIELD']];
					
					// LIMIT feature can be used on a column to limit a string to a certain number of characters
					$cell = ( (isset($value['LIMIT'])) && (strlen($cell)>$value['LIMIT']) ) ? $this->cellLIMIT($cell,$value['LIMIT'] ) : $cell ;
					
					// FUNCTION allows for a method to be applied to the cell value
					$cell = (isset($value['FUNCTION'])) ? $this->cellFUNCTION($cell,$value['FUNCTION']) : $cell ;
					
					// FORMAT allows for a predefined format to be applied to the cell value
					$cell = (isset($value['FORMAT'])) ? sprintf($value['FORMAT'], $cell) : $cell ;
					
					if (isset($value['REPLACE']))
					{
						foreach ($value['REPLACE'] as $field=>$replace)
						{
							$field_val = $info[$fields[$field]['DB_FIELD']];
							$cell = str_replace($replace,$field_val,$cell);
						}	
					}	
					
					$html .= $this->formatTDRow($value,$cell);
				}				
			}
			
			// End </tr>
			$html .= '</tr>'.LINE1;
		}

		// Don't forget to wrap the table body to go
		return $this->getTableBodyWrap($html);
	}
	
	
	public function getTableBodyJson()
	{
		$html    = '';
		$columns = $this->COLUMNS;	
		$data    = $this->getTableData();
		$fields  = $this->getFields();
		
		if (!$data) // in case there is an arror
		{
			return $html;
		}
		
		$table_array = array();
		
		foreach ($data as $row=>$info)
		{
			// Reset the $row_array each time
			$row_array = array();
			
			foreach ($columns as $key=>$value)
			{
				if ($this->checkGadget($value))
				{
					// Establish initial value of the cell. If key = ACTION, then start with a blank cell, FORMAT will take of it's value
					$cell = ($key==='ACTION') ? '' : $info[$fields[$key]['DB_FIELD']];
					
					// LIMIT feature can be used on a column to limit a string to a certain number of characters
					$cell = ( (isset($value['LIMIT'])) && (strlen($cell)>$value['LIMIT']) ) ? $this->cellLIMIT($cell,$value['LIMIT'] ) : $cell ;
					
					// FUNCTION allows for a method to be applied to the cell value
					$cell = (isset($value['FUNCTION'])) ? $this->cellFUNCTION($cell,$value['FUNCTION']) : $cell ;
					
					// FORMAT allows for a predefined format to be applied to the cell value
					$cell = (isset($value['FORMAT'])) ? sprintf($value['FORMAT'], $cell) : $cell ;
					
					if (isset($value['REPLACE']))
					{
						foreach ($value['REPLACE'] as $field=>$replace)
						{
							$field_val = $info[$fields[$field]['DB_FIELD']];
							$cell = str_replace($replace,$field_val,$cell);
						}	
					}
					
					$row_array[] = $cell;
				}				
			}
			
			$table_array[] = $row_array;
		}
		
		$final_array = array(
			'aaData' => $table_array);
			
		return json_encode($final_array);
	}
	
	private function getAddButton()
	{
		return sprintf($this->ADD_FORMAT,$this->ADD_WIDTH,$this->ADD_LINK);
	}
	
	// *******************************************************************
    // ********  CELL METHODS - AFFECT FORMATTING OF CELL ****************
    // *******************************************************************
    private function getTableData()
    {
	    $this->setDBConnection();
	    $order = $this->getTableOrder();  
	    return $this->db_connection->loadAllForTable($order);
    }
    
    private function getTableOrder()
    {
	    $this->setDBConnection();
	    $order_array = array();
	    
	    foreach ($this->ORDER as $value)
	    {
		    $order_array[] = $this->db_connection->getKey($value,'DB_FIELD');
	    }
	    
	    return implode(',', $order_array);
    }
	
	// *******************************************************************
    // ********  CELL METHODS - AFFECT FORMATTING OF CELL ****************
    // *******************************************************************
	public function cellLIMIT($cell, $limit)
	{
		return substr($cell, 0, $limit) . '...';
	}
	
	public function cellFUNCTION($cell, $function)
	{
		if (method_exists($this,$function))
		{
			$cell = call_user_func(array($this, $function), $cell);
		}
		
		return $cell;
	}
	
	public function formatCommaList($string)
	{
		// replaces comma lists that have no spaces with a comma and a space
		// so they will break in a display table and not push the table
		// out of it's div
		return str_replace(',', ', ', $string);
	}
	
	public function formatNoHTML($string)
	{
		return '<code>'.$string.'</code>';
	}
	
	public function formatTDRow($value,$cell)
	{
		$html  = '';
		$scope = (isset($value['SCOPE'])) ? ' scope="'.$value['SCOPE'].'"' : null ;
		$class = (isset($value['CLASS'])) ? ' class="'.$value['CLASS'].'"' : null ;
		$html .= '<td'.$scope.$class.'>'.$cell.'</td>'.LINE1;
		return $html;
	}
	
	// *******************************************************************
    // ********  FORMAT JAVASCRIPT READY STATEMENT FOR TABLE *************
    // *******************************************************************
	public function getJScript()
	{
		$jscript = '';
		$columns = $this->COLUMNS;

		foreach ($columns as $key=>$value)
		{
			if ($this->checkGadget($value))
			{
				$jscript .= (isset($value['JSCRIPT'])) ? $value['JSCRIPT'] . ',' : '' ;
			}
		}
		
		return substr($jscript,0,-1);
	}
	
	// *******************************************************************
    // **** checkGadget - Checks for DEVICE and RETURNS TRUE or FALSE ****
    // *******************************************************************
	public function checkGadget($value)
	{
		$go = true;
		
		if ( (isset($value['NO_SHOW'])) && (is_array($value['NO_SHOW'])) )
		{
			$device = $this->getDevice();
			
			foreach ($value['NO_SHOW'] as $gadget)
			{
				$go = ((isset($device[$gadget])) && ($device[$gadget])) ? false : $go ;
			}
		}
		
		return $go;
	}
}
?>