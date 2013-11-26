<?php
/*
	The article_tools class is never called alone, it is always extended from
	each of the article_detail class
	
*/

class article_tools extends details
{	
	public function loadArticle()
	{
		If (is_null($this->options['ARTICLE']))
		{
			$article = wed_getDBObject('content_connect');
			
			// $article = $this->db->loadDBObject('content_main');
			$status  = (!is_null($this->ID)) ? $article->loadArticleID($this->ID) : $article->loadArticle($this->CODE);
			
			if (!$status)
			{
				$article->loadArticle(wed_getSystemValue('CONTENT_NOT_FOUND_CODE'));
			}
			
			$this->options['ARTICLE'] = $article;
		}
	}
	
	public function getDetail($detail=null,$default=null)
	{
		$this->loadArticle();
		return $this->options['ARTICLE']->getDetail($detail,$default);
	}
	
	public function getValue($name=null,$default=null)
	{
		$this->loadArticle();
		return $this->options['ARTICLE']->getValue($name,$default=null);
	}
	
	public function getStatus($default='Draft')
	{
		return $this->getValue('status',$default);
	}
	
	public function getTitle($default='Not Available')
	{
		return $this->getValue('title',$default);
	}
	
	public function getKeywords($default=null)
	{
		return $this->getValue('keywords',$default);
	}
	
	public function getExcerpt()
	{
		$html = '';
		$html = $this->getValue('excerpt');
		
		if ( (empty($html)) || (is_null($html)) )
		{
			$html = eclipseLongStrings($this->getFullArticle(), $this->EXPT_LENGTH);
		}
		
		return $html;
	}
	
	public function getFullArticle()
	{
		return $this->getValue('fullarticle');
	}
}

?>