<?php

// Error Numbers
//
// E_ERROR = 1 Fatal error, halt program
// E_WARNING = 2 Non-fatal run time warning
// E_PARSE = 4 Parsing error
// E_NOTICE = 8 Notice of a possible error
// E_USER_ERROR = 256 Same as E_ERROR except generated by trigger_error()
// E_USER_WARNING = 512 Same as E_WARNING except generated by trigger_error()
// E_USER_NOTICE = 1024 Same as E_NOTICE except generated by trigger_error()
// E_STRICT = 2048 PHP notices that would improve your code
// E_DEPRECATED = 8192 Possible code that may not work in the future
// E_USER_DEPRECATED = 16384 User generated error using trigger_error

class Error
{
    public static $err_html = null;
    
    // CATCHABLE ERRORS
    public static function captureNormal( $number, $err_msg, $file, $line )
    {
        $message = 'ERROR TYPE: ' . $number . '<br>';
        $message .= 'MESSAGE: ' . $err_msg . '<br>';
        $message .= 'FILE: ' . $file . '<br>';
        $message .= 'LINE: ' . $line;
        
        $message = '<p>' . $message . '</p>';
        
        self::addMessage($message);
        
        // If the errors are FATAL, then go to the nice error screen
        if ( ($number===E_ERROR) || ($number===E_USER_ERROR) )
        {
	        // Fatal Errors - proceed to really bad error screen
	        $message = $number . '_' .$err_msg . '_' . $file . '_' . $line;
	        header('Location: /sites/system/index.php?display=error&message='.$message);
			exit();
	        
	        /*
if (!file_exists(dirname(__FILE__) . DS . 'sites' . DS . 'site_setup.php'))
	        {
		        include_once(dirname(__FILE__) . DS . 'sites' . DS . 'site_setup.php');
				$site_setup = new site_setup();
				
				$site = (!defined('SITE_DOMAIN')) ? 'system' : SITE_DOMAIN ;
				$site_setup->closeSiteError($site,$message);
	        }
	        else
	        {
		        header('Location: /sites/system/index.php?display=error&message='.$message);
				exit();
	        }
*/ 
        }
    }
    
    // EXTENSIONS
    public static function captureException( $exception )
    {
        $message = 'EXCEPTION TYPE: ' . $exception->getCode() . '<br>';
        $message .= 'MESSAGE: ' . $exception->getMessage() . '<br>';
        $message .= 'FILE: ' . $exception->getFile() . '<br>';
        $message .= 'LINE: ' . $exception->getLine();
        
        $message = '<p>' . $message . '</p>';
        
        self::addMessage($message);
    }
    
    // UNCATCHABLE ERRORS
    public static function captureShutdown( )
    {
        $error = error_get_last( );
        if ( $error )
        {
            // IF YOU WANT TO CLEAR ALL BUFFER, UNCOMMENT NEXT LINE:
            // ob_end_clean( );
            //
            // NOTE
            // The errors caught here are those that are available
            // when the program ends or an exit() is incountered.
            // Many of the E_DEPRECATED errors are listed when you
            // post these.
            
            $message = 'EXIT ERROR TYPE: ' . $error['type'] . '<br>';
            $message .= 'MESSAGE: ' . $error['message'] . '<br>';
            $message .= 'FILE: ' . $error['file'] . '<br>';
            $message .= 'LINE: ' . $error['line'];
        
			$safe_error_msg = $message;
			
            $message = '<p>' . $message . '</p>';
            
            // $safe_error_msg = '[' . $error['type'] . substr($error['file'], -9) . $error['line'] . ']';
            
            
            echo '<h1>Sorry, an error occurred.</h1> <p>Our staff is working to fix the error. Thanks for your patience.</p><p style="font-size:8px;">' . $safe_error_msg . '</p>';
            
            self::addMessage($message);
        }
        else
        { 
        	return true;
        }
    }
    
    public static function addMessage($message=null)
    {
	    if (!is_null($message))
	    {
		    if (is_null(self::$err_html))
		    {
			    self::$err_html = $message;
		    }
		    else
		    {
			    self::$err_html .= $message;
		    } 
	    }
    }
    
    public static function getMessage()
    {
	    if (!is_null(self::$err_html))
	    {
		    // Change the style to match the style below once you are ready to go live
		    return '<div style="background-color:#e9ec8f;padding:10px;">' . self::$err_html . '</div>';
	    }
	    else
	    {
		    return '<div style="display:none;">No Errors occurred during this session.</div>';
	    }
    }
}

error_reporting( E_ALL | E_STRICT);
ini_set( 'display_errors', 0 );
ini_set( 'log_errors' , 1 );
ini_set( 'log_errors_max_len', 0 );
ini_set( 'error_log', './wed_error_log.txt' );


set_error_handler( array( 'Error', 'captureNormal' ) );
set_exception_handler( array( 'Error', 'captureException' ) );
register_shutdown_function( array( 'Error', 'captureShutdown' ) );