<?php
/*
	The form_tools class is never called alone, it is always extended from
	each of the form controllers
	
*/

class form_tools
{
	public function addOptions($options)
	{
		$this->options = array_merge( $this->options, $options);
	}
	
	public function updateOption($name, $value)
	{
		$this->options[$name] = $value;
	}
	
	protected function loadView()
	{
		$html = '';
		
		$path = $this->options['LOCAL_PATH'].DS.$this->options['FORM_VIEW'];
		
		if (file_exists($path))
		{
			ob_start();
			@include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}
		
		return $html;
	}
	
	protected function loadFormElements($html)
	{
		$elements = $this->setFormElements();
		
		$keys   = array_keys($elements);
		$values = array_values($elements);
		
		$func = function($value) {
    		return '[{'.$value.'}]';
		};
		
		$keys = array_map($func, $keys);
		
		$html = str_replace($keys, $values, $html);
		
		return $html;
	}
	
	protected function loadFormValues($html)
	{
		$fieldlist = $this->tbl->options['FIELDS'];		
		$checklist = array('LABEL','VALUE','INSTRUCT','MESSAGE','LIST-SELECT');
		$look      = array();
		
		$func = function($value) {
    		return '[{'.$value.'}]';
		};
		
		foreach ($fieldlist as $key=>$value)
		{
			foreach ($checklist as $type)
			{
				$new_key = $func($key.':'.$type);
				
				if ($type==='VALUE')
				{
					$look[$new_key] = $this->getFieldValue($value);
				}
				elseif ($type==='LIST-SELECT')
				{
					$look[$new_key] = $this->getListSelect($value);
				}
				else
				{
					$look[$new_key] = (isset($value[$type])) ? $value[$type] : '';
				}	
			}
		}
		
		$keys   = array_keys($look);
		$values = array_values($look);
		$html   = str_replace($keys, $values, $html);
		
		return $html;
	}
	
	protected function getFieldValue($value)
	{
		$val = '';
		
		if (isset($value['VALUE']))
		{
			$val = $value['VALUE'];
		}
		elseif (isset($value['DEFAULT']))
		{
			$val = $value['DEFAULT'];
		}
		
		return $val;
	}
	
	protected function getListSelect($value)
	{
		$html       = '';
		$select_val = '';
		
		if (!isset($value['LIST-SELECT']))
		{
			return $html;
		}
		
		if (isset($value['VALUE']))
		{
			$select_val = $value['VALUE'];			
		}
		elseif (isset($value['DEFAULT']))
		{
			$select_val = $value['DEFAULT'];
		}
		
		if (is_array($value['LIST-SELECT']))
		{
			foreach ($value['LIST-SELECT'] as $list_key=>$list_value)
			{
				$selected = ($select_val===$list_value) ? ' selected="selected"' : '' ;
				$html .= '<option value="'.$list_key.'"'.$selected.'>'.$list_value.'</option>'.LINE1;
			}
		}
		else
		{
			// Future improvement here - pull list from another source
		}
		
		return $html;
	}
	
	public function getHTML()
	{
		return null;
	}
}

?>