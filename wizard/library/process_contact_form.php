<?php
defined( '_GOOFY' ) or die();
if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n");
/*
 *
 *
 *
 *
 *
 */
 
class process_contact_form
{
	public $options;
	public $headers;
	
	public function __construct()
	{
		$this->setOptions();
	}
	
	public function setOptions()
	{
		$this->options['EMAIL']          = (isset($_POST['email'])) ? $_POST['email'] : null;
		$this->options['NAME']           = (isset($_POST['name'])) ? $_POST['name'] : 'Subscriber';
		$this->options['TEMPLATE_USER']  = 'contact_response_user';
		$this->options['TEMPLATE_STAFF'] = 'contact_response_staff';
	}
	
	public function setHeaders()
	{
		$email    = $this->options['EMAIL'];
		$headers  = 'MIME-Version: 1.0' . PHP_EOL;
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;  
		$headers .= "From: $email" . PHP_EOL;
		$headers .= "Reply-To: $email" . PHP_EOL;
		$this->headers = $headers;
	}
	
	public function getMessageTemplate()
	{
		
	}
	
	public function getHTML()
	{
		if (!is_null($this->options['EMAIL']))
		{
			$this->setHeaders();
		}
		
	}

}

$form = new process_contact_form();
$html = $form->getHTML();

echo $html;





$email = $_POST['email'];

var_dump($_POST);

// $merge_vars = array( 'YNAME' => $_POST['yname'] );

$headers  = 'MIME-Version: 1.0' . PHP_EOL;
$headers .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;  
$headers .= "From: $email" . PHP_EOL;
$headers .= "Reply-To: $email" . PHP_EOL;

$subject = 'ASF Foundation Newsletter';

$msg     = 'Thank you!'; // include 'newsletter_template.php';

mail($email, $subject, $msg, $headers);

$staff_email = 'chris.wilkins@alagames.com,randyc@alagames.com,tommyk@alagames.com';
$subject     = 'Add email: '.$email. ' to the newsletter list';

mail($staff_email, $subject, $msg, $headers);

echo "Got it, you've been added to our email list.";

?>