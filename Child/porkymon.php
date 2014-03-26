<?php
/**
 * Created by PhpStorm.
 * User: camilord
 * Date: 3/26/14
 * Time: 2:15 PM
 */

// CONFIGURATION =======================================================================================================
/*
 * Data receiver where the porkymon parent application resides.
 */
define('PARENT_APP', 'porkymon.abcs.org.nz');
// do not change this part...
define('DATA_RECEIVER_URL', 'http://'.PARENT_APP.'/gateway/receive');

/*
 * client name - use for authentication
 * warning: this value must be the same with "hostname"
 *          if not the same, data will be rejected
 */
define('CHILD_NAME','abcs.co.nz');

/*
 * use to communicate or authenticate or verify that the data belongs to
 * the parent porkymon.
 *
 * good as password :)
 *
 * you can get this value from the parent app when you add the child server.
 */
define('SECRET_KEY','F7PEb8krLJ2KiFbTeq4sg1rLBCnU8zBK');

/*
 * salt key - key for encryption of data
 * note: this must be the same on the parent app...
 */
define('PORKEY','LXlkM5iaxi69lOIcvi5iaQnNpCCsQnzN');

/*
 * DEBUG MODE
 */
define('DEBUG_MODE','1');


// CLASSES =============================================================================================================

class Crypt
{
    var $skey = PORKEY;

    public function safe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public function safe_b64decode($string)
    {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public function encode($value)
    {
        if(!$value){return false;}

        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);

        return trim($this->safe_b64encode($crypttext));
    }

    public function decode($value)
    {
        if(!$value){return false;}

        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);

        return trim($decrypttext);
    }
}

/**
 * CodeIgniter Curl Class
 *
 * Work with remote servers via cURL much easier than using the native PHP bindings.
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Philip Sturgeon
 * @license         http://philsturgeon.co.uk/code/dbad-license
 * @link			http://philsturgeon.co.uk/code/codeigniter-curl
 */
class Curl {

    protected $_ci;                 // CodeIgniter instance
    protected $response = '';       // Contains the cURL response for debug
    protected $session;             // Contains the cURL handler for a session
    protected $url;                 // URL of the session
    protected $options = array();   // Populates curl_setopt_array
    protected $headers = array();   // Populates extra HTTP headers
    public $error_code;             // Error code returned as an int
    public $error_string;           // Error message returned as a string
    public $info;                   // Returned after request (elapsed time, etc)

    function __construct($url = '')
    {
        //log_message('debug', 'cURL Class Initialized');

        if ( ! $this->is_enabled())
        {
            //log_message('error', 'cURL Class - PHP was not built with cURL enabled. Rebuild PHP with --with-curl to use cURL.');
        }

        $url AND $this->create($url);
    }

    public function __call($method, $arguments)
    {
        if (in_array($method, array('simple_get', 'simple_post', 'simple_put', 'simple_delete')))
        {
            // Take off the "simple_" and past get/post/put/delete to _simple_call
            $verb = str_replace('simple_', '', $method);
            array_unshift($arguments, $verb);
            return call_user_func_array(array($this, '_simple_call'), $arguments);
        }
    }

    /* =================================================================================
     * SIMPLE METHODS
     * Using these methods you can make a quick and easy cURL call with one line.
     * ================================================================================= */

    public function _simple_call($method, $url, $params = array(), $options = array())
    {
        // Get acts differently, as it doesnt accept parameters in the same way
        if ($method === 'get')
        {
            // If a URL is provided, create new session
            $this->create($url.($params ? '?'.http_build_query($params, NULL, '&') : ''));
        }

        else
        {
            // If a URL is provided, create new session
            $this->create($url);

            $this->{$method}($params);
        }

        // Add in the specific options provided
        $this->options($options);

        return $this->execute();
    }

    public function simple_ftp_get($url, $file_path, $username = '', $password = '')
    {
        // If there is no ftp:// or any protocol entered, add ftp://
        if ( ! preg_match('!^(ftp|sftp)://! i', $url))
        {
            $url = 'ftp://' . $url;
        }

        // Use an FTP login
        if ($username != '')
        {
            $auth_string = $username;

            if ($password != '')
            {
                $auth_string .= ':' . $password;
            }

            // Add the user auth string after the protocol
            $url = str_replace('://', '://' . $auth_string . '@', $url);
        }

        // Add the filepath
        $url .= $file_path;

        $this->option(CURLOPT_BINARYTRANSFER, TRUE);
        $this->option(CURLOPT_VERBOSE, TRUE);

        return $this->execute();
    }

    /* =================================================================================
     * ADVANCED METHODS
     * Use these methods to build up more complex queries
     * ================================================================================= */

    public function post($params = array(), $options = array())
    {
        // If its an array (instead of a query string) then format it correctly
        if (is_array($params))
        {
            //$params .= http_build_query($params, NULL, '&');
            $new_params = '';
            foreach ($params as $key => $val) {
                $new_params .= ($new_params == '') ? '' : '&';
                if (is_array($val)) {
                    foreach ($val as $item) {
                        $new_params .= ($new_params == '') ? '' : '&';
                        $new_params .= urlencode($key).'[]='.urlencode($item);
                    }
                } else {
                    $new_params .= urlencode($key).'='.urlencode($val);
                }
            }
            $params = $new_params;
            unset($new_params);
            //echo urldecode($params).'<hr />';
        }

        // Add in the specific options provided
        $this->options($options);

        $this->http_method('post');

        $this->option(CURLOPT_POST, TRUE);
        $this->option(CURLOPT_POSTFIELDS, $params);
    }

    public function put($params = array(), $options = array())
    {
        // If its an array (instead of a query string) then format it correctly
        if (is_array($params))
        {
            $params = http_build_query($params, NULL, '&');
        }

        // Add in the specific options provided
        $this->options($options);

        $this->http_method('put');
        $this->option(CURLOPT_POSTFIELDS, $params);

        // Override method, I think this overrides $_POST with PUT data but... we'll see eh?
        $this->option(CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT'));
    }

    public function delete($params, $options = array())
    {
        // If its an array (instead of a query string) then format it correctly
        if (is_array($params))
        {
            $params = http_build_query($params, NULL, '&');
        }

        // Add in the specific options provided
        $this->options($options);

        $this->http_method('delete');

        $this->option(CURLOPT_POSTFIELDS, $params);
    }

    public function set_cookies($params = array())
    {
        if (is_array($params))
        {
            $params = http_build_query($params, NULL, '&');
        }

        $this->option(CURLOPT_COOKIE, $params);
        return $this;
    }

    public function http_header($header, $content = NULL)
    {
        $this->headers[] = $content ? $header . ': ' . $content : $header;
    }

    public function http_method($method)
    {
        $this->options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
        return $this;
    }

    public function http_login($username = '', $password = '', $type = 'any')
    {
        $this->option(CURLOPT_HTTPAUTH, constant('CURLAUTH_' . strtoupper($type)));
        $this->option(CURLOPT_USERPWD, $username . ':' . $password);
        return $this;
    }

    public function proxy($url = '', $port = 80)
    {
        $this->option(CURLOPT_HTTPPROXYTUNNEL, TRUE);
        $this->option(CURLOPT_PROXY, $url . ':' . $port);
        return $this;
    }

    public function proxy_login($username = '', $password = '')
    {
        $this->option(CURLOPT_PROXYUSERPWD, $username . ':' . $password);
        return $this;
    }

    public function ssl($verify_peer = TRUE, $verify_host = 2, $path_to_cert = NULL)
    {
        if ($verify_peer)
        {
            $this->option(CURLOPT_SSL_VERIFYPEER, TRUE);
            $this->option(CURLOPT_SSL_VERIFYHOST, $verify_host);
            $this->option(CURLOPT_CAINFO, $path_to_cert);
        }
        else
        {
            $this->option(CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        return $this;
    }

    public function options($options = array())
    {
        // Merge options in with the rest - done as array_merge() does not overwrite numeric keys
        foreach ($options as $option_code => $option_value)
        {
            $this->option($option_code, $option_value);
        }

        // Set all options provided
        @curl_setopt_array($this->session, $this->options);

        return $this;
    }

    public function option($code, $value)
    {
        if (is_string($code) && !is_numeric($code))
        {
            $code = constant('CURLOPT_' . strtoupper($code));
        }

        $this->options[$code] = $value;
        return $this;
    }

    // Start a session from a URL
    public function create($url)
    {
        // If no a protocol in URL, assume its a CI link
        if ( ! preg_match('!^\w+://! i', $url))
        {
            $this->_ci->load->helper('url');
            $url = site_url($url);
        }

        $this->url = $url;
        $this->session = curl_init($this->url);

        return $this;
    }

    // End a session and return the results
    public function execute()
    {
        // Set two default options, and merge any extra ones in
        if ( ! isset($this->options[CURLOPT_TIMEOUT]))
        {
            $this->options[CURLOPT_TIMEOUT] = 30;
        }
        if ( ! isset($this->options[CURLOPT_RETURNTRANSFER]))
        {
            $this->options[CURLOPT_RETURNTRANSFER] = TRUE;
        }
        if ( ! isset($this->options[CURLOPT_FAILONERROR]))
        {
            $this->options[CURLOPT_FAILONERROR] = TRUE;
        }

        // Only set follow location if not running securely
        if ( ! ini_get('safe_mode') && ! ini_get('open_basedir'))
        {
            // Ok, follow location is not set already so lets set it to true
            if ( ! isset($this->options[CURLOPT_FOLLOWLOCATION]))
            {
                $this->options[CURLOPT_FOLLOWLOCATION] = TRUE;
            }
        }

        if ( ! empty($this->headers))
        {
            $this->option(CURLOPT_HTTPHEADER, $this->headers);
        }

        $this->options();

        // Execute the request & and hide all output
        $this->response = curl_exec($this->session);
        $this->info = curl_getinfo($this->session);

        // Request failed
        if ($this->response === FALSE)
        {
            $errno = curl_errno($this->session);
            $error = curl_error($this->session);

            curl_close($this->session);
            $this->set_defaults();

            $this->error_code = $errno;
            $this->error_string = $error;

            return FALSE;
        }

        // Request successful
        else
        {
            curl_close($this->session);
            $this->last_response = $this->response;
            $this->set_defaults();
            return $this->last_response;
        }
    }

    public function is_enabled()
    {
        return function_exists('curl_init');
    }

    public function debug()
    {
        echo "=============================================<br/>\n";
        echo "<h2>CURL Test</h2>\n";
        echo "=============================================<br/>\n";
        echo "<h3>Response</h3>\n";
        echo "<code>" . nl2br(htmlentities($this->last_response)) . "</code><br/>\n\n";

        if ($this->error_string)
        {
            echo "=============================================<br/>\n";
            echo "<h3>Errors</h3>";
            echo "<strong>Code:</strong> " . $this->error_code . "<br/>\n";
            echo "<strong>Message:</strong> " . $this->error_string . "<br/>\n";
        }

        echo "=============================================<br/>\n";
        echo "<h3>Info</h3>";
        echo "<pre>";
        print_r($this->info);
        echo "</pre>";
    }

    public function debug_request()
    {
        return array(
            'url' => $this->url
        );
    }

    public function set_defaults()
    {
        $this->response = '';
        $this->headers = array();
        $this->options = array();
        $this->error_code = NULL;
        $this->error_string = '';
        $this->session = NULL;
    }
}

// FUNCTIONS ===========================================================================================================

function submitData($data) {
    $crypt = new Crypt();
    $curl = new Curl(DATA_RECEIVER_URL);
    // set curl options...
    $curl_options = array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSLVERSION => 3,
        CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_AUTOREFERER => 1,
        CURLOPT_RETURNTRANSFER => 1
    );

    $post_data = array(
        'child_auth' => $crypt->encode(serialize(array(CHILD_NAME, SECRET_KEY))),
        'child_data' => $crypt->encode(serialize($data))
    );
    $json_data = $curl->post($post_data, $curl_options);

    return json_decode($json_data, true);
}

function system_function() {
    $functions = array('system','shell_exec','passthru','exec');
    $use_function = null;
    foreach ($functions as $func) {
        if (function_exists($func)) {
            $use_function = $func;
            break;
        }
    }
    return $use_function;
}

// PROCESS / INITIALISATION ============================================================================================
/*
 * initiate porkymon
 */
$init_function = system_function();

if (is_null($init_function)) {
    echo "\n\n".'Unable to collect data from the server!'."\n\n";
} else {
    $collected_data = array();

    // get or include timestamp
    $collected_data[] = time();

    // set of commands to get system info
    $commands = array(
        'ifconfig eth0 | grep \'inet addr:\' | cut -d: -f2 | awk \'{ print $1}\'',
        'hostname',
        'free',
        'df',
        'uptime'
    );

    // execute commands...
    echo 'Executing command: '.$init_function.'()'."\n";
    foreach ($commands as $cmd) {
        if (in_array($cmd, array('passthru','exec'))) {
            $init_function($cmd, $output_data);
        } else {
            ob_start();
            $init_function($cmd);
            $output_data = ob_get_contents();
            ob_end_clean();
        }
        $collected_data[] = $output_data;
    }
    if (DEBUG_MODE == 1) {
        print_r($collected_data);
    }
    echo "Execution Completed!\n\n";

    // submit data to parent app
    echo "Sending Data to App Receiver... \n";
    $post_response = submitData($collected_data);
    if (DEBUG_MODE == 1) {
        print_r($post_response);
    }
    echo "\nSending Completed.\n\n";
}

echo "\n";