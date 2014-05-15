<?php
defined( '_GOOFY' ) or die();

$html = '';

$communicator = getImagineer('communications');

$options['TYPE']       = 'email';
$options['CODE']       = 'newsletter_response_user';
$options['EMAIL']      = (isset($_POST['email'])) ? $_POST['email'] : null;
$options['EMAIL_NAME'] = (isset($_POST['name'])) ? $_POST['name'] : 'Subscriber';
$options['SUBJECT']    = 'Thank You';

$config = new detail_object($options);
$id     = $communicator->newCommunication($config);

if ($communicator->getHTML(array('ID' => $id)))
{
	$response = "Thank you, you've been added to our e-newsletter list.";
	
	// Send Staff Response
	$staff               = array(); // Clear the options array
	$staff['TYPE']       = 'email';
	$staff['CODE']       = 'newsletter_response_staff';
	$staff['EMAIL_NAME'] = (isset($_POST['name'])) ? $_POST['name'] : 'Staff';
	$staff['SUBJECT']    = 'New Newsletter Subscriber: '. $options['EMAIL_NAME'] . '<' . $options['EMAIL'] . '>' ;

	$config = new detail_object($staff);
	$id     = $communicator->newCommunication($config);
	$communicator->getHTML(array('ID' => $id));
}
else
{
	$response = 'Sorry, an error occurred. Please try again';
}

return $response;

?>