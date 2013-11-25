<?php
/*
 * fm_common.php
 *
 * This class will be extended by each of the fm objects and
 * provide a common set of tools for each object.
 *
 */

defined( '_GOOFY' ) or die();


class fm_common
{
	public function getValue($name,$default=null)
	{
		$name = strtolower($name);
		return (isset($this->options['FIELDS'][$name]['VALUE'])) ? $this->options['FIELDS'][$name]['VALUE'] : $default ;
	}
	
	public function addOptions($options)
	{
		$this->options = array_merge($this->options,$options);
	}
	
	public function getTableStart()
	{
		return null;
	}
	
	public function getTableFooter()
	{
		return null;
	}
	
	public function getColumns()
	{
		return null;
	}
	
	public function getTableHead()
	{
		$html  = '';
		$line  = (isset($this->options['LINE_FORMAT'])) ? $this->options['LINE_FORMAT'] : null ;
		$add   = (isset($this->options['ADD_BUTTON'])) ? $this->options['ADD_BUTTON'] : null ;
		
		$columns = $this->getColumns();
		
		foreach ($columns as $key=>$value)
		{
			$width = (isset($value['WIDTH'])) ? $value['WIDTH'] : '20%' ;
			$title = (isset($value['TITLE'])) ? $value['TITLE'] : 'Column' ;
			$html .= ($key!='ACTION') ? sprintf($line,$width,$title) : '' ;
		}
		
		$html .= $add;
		
		$html = '<thead>'.LINE1.'<tr>'.LINE1.$html.'</tr>'.LINE1.'</thead>'.LINE2;
		
		return $html;			
	}
	
	public function process_Actions($name, $data=array())
	{
		$action = 'action_'.$name;
		
		if (method_exists($this,$action))
		{
			return call_user_func(array($this, $action), $data);
		}
		else
		{
			return 'Information Unavailable';
		}
	}
	
	public function loadFormValues($fields,$html)
	{
		foreach ($fields as $key=>$values)
		{
			foreach ($values as $attr=>$val)
			{
				if (!is_array($val))
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
	
	public function setHTML($options)
	{
		return null;
	}
	
	public function getHTML($options)
	{
		return $this->setHTML($options);
	}

}


?>