<?php
/*
 * accountant_form_tools.php
 *
 * This class will be extended by each of the fm objects and
 * provide a common set of tools for each object.
 *
 */

defined( '_GOOFY' ) or die();


class accountant_form_tools extends details
{	
	public function checkFormOptions()
	{
		$result = true;
		$result = (is_null($this->options['ID'])) ? false : $result;
		$result = (is_null($this->db_connection)) ? false : $result;
		return $result;
	}
	
	public function getUrl($mode='save',$use_id=true)
	{
		$key = $this->options['KEY'];
		$id  = ($use_id) ? $this->options['ID'] : '' ;
		return sprintf($this->options['URL_FORMAT'],$key,$mode,$id);
	}
	
	public function getFields()
	{
		$fields = array();
		
		if (!is_null($this->db_connection))
		{
			$fields = $this->db_connection->FIELDS;
		}
		
		return $fields;
	}
	
	public function executeForm()
	{
		$this->setDBConnection();
		
		if (!$this->checkFormOptions())
		{
			$this->options['RETURN_VAL'] = 'FORM-ERROR';
			$this->options['RETURN']     = true;
		}
		else
		{
			$method = $this->MODE.'Form';
			if (method_exists($this,$method))
			{
				call_user_func(array($this,$method));
			}
		}
	}
	
	private function newForm()
	{
		$this->db_connection->addValues_Default();
		$this->options['ID'] = $this->db_connection->insertNewRecord();
	}
	
	private function saveForm()
	{
		$this->db_connection->addValues_Request();
		$this->options['RETURN_VAL'] = $this->db_connection->updateRecordID($this->options['ID']);
		$this->options['RETURN']     = true;
	}
	
	private function deleteForm()
	{
		$this->options['RETURN_VAL'] = $this->db_connection->deleteByID($this->options['ID']);
		$this->options['RETURN']     = true;
	}
	
	private function editForm()
	{
		$data = $this->db_connection->loadRecordID($this->options['ID']);
		
		if (!$data)
		{
			$this->options['RETURN_VAL'] = 'FORM-ERROR';
			$this->options['RETURN']     = true;
		}
	}
	
	public function getFieldValue($field=null)
	{
		$fields = $this->getFields();
		return (isset($fields[$field]['VALUE'])) ? $fields[$field]['VALUE'] : '' ;
	}
	
	public function getFieldSelectList($field=null)
	{
		$fields = $this->getFields();
		return (isset($fields[$field]['LIST-SELECT'])) ? $fields[$field]['LIST-SELECT'] : array() ;
	}
	
	public function getSelectListHTML($field=null)
	{
		$html = '';
		
		$field_value = $this->getFieldValue($field);
		$field_list  = $this->getFieldSelectList($field);
		
		foreach ($field_list as $key=>$value)
        {
            $selected = ($key===$field_value) ? 'selected' : '' ;
            $html .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
            
        }
        
        return $html;
	}
	
	// *******************************************************************
    // **** loadFormValues - loads values into form template *************
    // *******************************************************************
	public function loadFormValues($html)
	{
		$fields = $this->getFields();
		
		foreach ($fields as $key=>$values)
		{
			foreach ($values as $attr=>$val)
			{
				if (is_null($val))
				{
					$html = str_replace('[('.$key.'_'.$attr.')]', '', $html);
				}
				elseif (!is_array($val))
				{
					$html = str_replace('[('.$key.'_'.$attr.')]', $val, $html);
				}
			}
		}
		
		return $html;
	}
	
	public function getJavascript()
	{
		return null;
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