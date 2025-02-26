<?php
namespace AWS\Controller;
/**
 * Simple Class to manage Egnyte uploads with the Egnyte public API (https://developer.egnyte.com)
 */
Class EgnyteClient {
	
	protected $oauthToken;
	protected $domain;
	protected $baseUrl;
	protected $curl;

	/**
	 * Instantiates the Egnyte Client
	 * @param string $domain     Egnyte domain name, e.g. mycompany
	 * @param string $oauthToken oAuth token associated with the user for whom the actions will be performed
	 */
	public function __construct($domain, $oauthToken) {
		if(!extension_loaded('curl')) {
			throw new Exception('EgnyteClient requires the PHP Curl extension to be enabled');
		}

		$this->domain = $domain;
		$this->oauthToken = $oauthToken;
		$this->baseUrl = 'https://' . $domain . '.egnyte.com/pubapi/v1';

		$this->curl = new Curl;
		
		// set an HTTP header with the oAuth token
		$this->curl->headers['Authorization'] = "Bearer $oauthToken";

		// real deploymnets should do SSL verification, but for simplicity this is turned off
		// since PHP's curl extension (at least on Windows) does not have certificates setup by default
		$this->curl->options['CURLOPT_SSL_VERIFYPEER'] = false;
		
	}

	/**
	 * Upload a file to Egnyte
	 * @param  string $cloudPath    Folder path where the file should be uploaded, including trailing slash
	 * @param  string $fileName     File name for the file
	 * @param  string $fileContents Binary contents of the file
	 * @return EgnyteResponse       Response object
	 */
	public function uploadFile($cloudPath, $fileName, $fileContents) {
		// path names are in the URL, so they need to be encoded
		$path = self::encodePath($cloudPath . $fileName);
		// set a content type for the upload: application/octet-stream can safely be used for all file types since we are sending binary data
		$this->curl->headers['Content-Type'] = "application/octet-stream";

		// send the api request and return the HTTP response from the server
		$response = $this->post("/fs-content" . $path, $fileContents, array(
			400 => 'Bad request - missing parameters, file filtered out (e.g. .tmp file) or file is too large (>100 MB)',
			401 => 'User not authorized',
			403 => 'Not enough permissions / forbidden file upload location ( e.g. /, /Shared, /Private etc.)'
		));
		return $response;
	}
	/**
	 * @param unknown $cloudPath
	 * @param unknown $fileName
	 */
	public function createLink($fileName){
		// path names are in the URL, so they need to be encoded
		$postFields = array();
		$postFields['path'] = "$fileName";
		$postFields['type'] = 'file';
		$postFields['accessibility'] = 'anyone';
		// send the api request and return the HTTP response from the server
		$response = $this->postJSON("/links", $postFields, array());
		/**
		 * 
		 * 2015-09-11
		 * {"message":"Standard Users cannot create links with this API"}
		 * 
		 * 
		 */
		return $response;		
	}
	/**
	 * Carregando arquivo
	 * @param unknown $path
	 */
	public function downloadFile($path){
		return $this->curl->get($this->baseUrl . '/fs-content/'.self::encodePath($path));
	}

	/**
	 * Get the metadata for a file
	 * @param  string $path The full path to a file in the cloud
	 * @return EgnyteResponse       Response object
	 */
	public function getFileDetails($path) {
		return $this->get('/fs' . self::encodePath($path));
	}

	/**
	 * Create a new folder
	 * @param  string $parentFolder parent folder path including trailing slash
	 * @param  string $name         name of the new folder
	 * @return EgnyteResponse       Response object
	 */
	public function createFolder($parentFolder, $name) {
		$path = self::encodePath($parentFolder . $name);
		return $this->postJSON('/fs' . $path, array('action' => 'add_folder'), array(
			403 => 'User does not have permission to create folder',
			405 => 'A file with the same name already exists'
		));
	}

	protected function get($url, $errorMap = array()) {
		return new EgnyteResponse($this->curl->get($this->baseUrl . $url), $errorMap);
	}

	protected function post($url, $postFields = array(), $errorMap = array()) {
		return new EgnyteResponse($this->curl->post($this->baseUrl . $url, $postFields), $errorMap);
	}

	protected function postJSON($url, $json = array(), $errorMap = array()) {
		$this->curl->headers['Content-Type'] = "application/json";
		return $this->post($url, json_encode($json), $errorMap);
	}


	/**
	 * Encodes paths so they can be used in URLs
	 * @param  string $path Folder path, optionally including file name
	 * @return string       The encoded path
	 */
	public static function encodePath($path) {
		return str_replace('+','%20',implode('/',array_map('urlencode', explode('/', $path))));
	}

}

/**
 * A wrapper around the http response that provides easy access to attributes of the response including error information
 */
Class EgnyteResponse {

	public $curlResponse;
	public $statusCode;
	public $body;
	public $errorMap = array(
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		415 => 'Unsupported Media Type',
		500 => 'Internal Server Error',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		596 => 'Service Not Found'
	);


	public function __construct($curlResponse, $errorMap = array()) {
		$this->curlResponse = $curlResponse;
		$this->errorMap = $this->errorMap + $errorMap;
		$this->body = $curlResponse->body;
		$this->statusCode = (int) $this->curlResponse->headers['Status-Code'];
	}

	/**
	 * Whether the request was an error
	 * @return boolean True if error, false if successful
	 */
	public function isError() {
		return $this->statusCode >= 400;
	}

	/**
	 * JSON decode the body of the response
	 * @return StdClass A decoded version of the JSON response.  Null if the response can't be JSON decoded
	 */
	public function getDecodedJSON() {
		return json_decode($this->body);
	}

	/**
	 * Details on errors, should not be called on successful requests
	 * @return array associated array of fields with error information
	 */
	public function getErrorDetails() {
		if($this->statusCode < 400) {
			return new Exception('Request was successful, there are no error details');
		}
		$fields = array(
			'rawBody' => $this->curlResponse->body,
			'jsonBody' => $this->getDecodedJSON(),
			'statusCode' => $this->statusCode,
			'statusCodeText' => (array_key_exists($this->statusCode, $this->errorMap)) ? $this->errorMap[$this->statusCode] : 'Unknown Error'
		);


		if(isset($this->curlResponse->headers['X-Mashery-Error-Code'])) {
			$fields['apiException'] = $this->curlResponse->headers['X-Mashery-Error-Code'];
		}
		return $fields;
	}
}

/**
 * A basic CURL wrapper
 *
 * See the README for documentation/examples or http://php.net/curl for more information about the libcurl extension for PHP
 *
 * @package curl
 * @author Sean Huber <shuber@huberry.com>
 **/
class Curl {

	/**
	 * The file to read and write cookies to for requests
	 *
	 * @var string
	 **/
	public $cookie_file;

	/**
	 * Determines whether or not requests should follow redirects
	 *
	 * @var boolean
	 **/
	public $follow_redirects = true;

	/**
	 * An associative array of headers to send along with requests
	 *
	 * @var array
	 **/
	public $headers = array();

	/**
	 * An associative array of CURLOPT options to send along with requests
	 *
	 * @var array
	**/
	public $options = array();

	/**
	 * The referer header to send along with requests
	 *
	 * @var string
	**/
	public $referer;

	/**
	 * The user agent to send along with requests
	 *
	 * @var string
	 **/
	public $user_agent;

	/**
	 * Stores an error string for the last request if one occurred
	 *
	 * @var string
	 * @access protected
	 **/
	protected $error = '';

	/**
	 * Stores resource handle for the current CURL request
	 *
	 * @var resource
	 * @access protected
	 **/
	protected $request;

	/**
	 * Initializes a Curl object
	 *
	 * Sets the $cookie_file to "curl_cookie.txt" in the current directory
	 * Also sets the $user_agent to $_SERVER['HTTP_USER_AGENT'] if it exists, 'Curl/PHP '.PHP_VERSION.' (http://github.com/shuber/curl)' otherwise
	 **/
	function __construct() {
		$this->cookie_file = dirname(__FILE__).DIRECTORY_SEPARATOR.'curl_cookie.txt';
		$this->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Curl/PHP '.PHP_VERSION.' (http://github.com/shuber/curl)';
	}

	/**
	 * Makes an HTTP DELETE request to the specified $url with an optional array or string of $vars
	 *
	 * Returns a CurlResponse object if the request was successful, false otherwise
	 *
	 * @param string $url
	 * @param array|string $vars
	 * @return CurlResponse object
	 **/
	function delete($url, $vars = array()) {
		return $this->request('DELETE', $url, $vars);
	}

	/**
	 * Returns the error string of the current request if one occurred
	 *
	 * @return string
	 **/
	function error() {
		return $this->error;
	}

	/**
	 * Makes an HTTP GET request to the specified $url with an optional array or string of $vars
	 *
	 * Returns a CurlResponse object if the request was successful, false otherwise
	 *
	 * @param string $url
	 * @param array|string $vars
	 * @return CurlResponse
	 **/
	function get($url, $vars = array()) {
		if (!empty($vars)) {
			$url .= (stripos($url, '?') !== false) ? '&' : '?';
			$url .= (is_string($vars)) ? $vars : http_build_query($vars, '', '&');
		}
		return $this->request('GET', $url);
	}

	/**
	 * Makes an HTTP HEAD request to the specified $url with an optional array or string of $vars
	 *
	 * Returns a CurlResponse object if the request was successful, false otherwise
	 *
	 * @param string $url
	 * @param array|string $vars
	 * @return CurlResponse
	 **/
	function head($url, $vars = array()) {
		return $this->request('HEAD', $url, $vars);
	}

	/**
	 * Makes an HTTP POST request to the specified $url with an optional array or string of $vars
	 *
	 * @param string $url
	 * @param array|string $vars
	 * @return CurlResponse|boolean
	 **/
	function post($url, $vars = array()) {
		return $this->request('POST', $url, $vars);
	}

	/**
	 * Makes an HTTP PUT request to the specified $url with an optional array or string of $vars
	 *
	 * Returns a CurlResponse object if the request was successful, false otherwise
	 *
	 * @param string $url
	 * @param array|string $vars
	 * @return CurlResponse|boolean
	 **/
	function put($url, $vars = array()) {
		return $this->request('PUT', $url, $vars);
	}

	/**
	 * Makes an HTTP request of the specified $method to a $url with an optional array or string of $vars
	 *
	 * Returns a CurlResponse object if the request was successful, false otherwise
	 *
	 * @param string $method
	 * @param string $url
	 * @param array|string $vars
	 * @return CurlResponse|boolean
	 **/
	function request($method, $url, $vars = array()) {
		$this->error = '';
		$this->request = curl_init();
		if (is_array($vars)) $vars = http_build_query($vars, '', '&');
		$this->set_request_method($method);
		$this->set_request_options($url, $vars);
		$this->set_request_headers();

		$response = curl_exec($this->request);

		if ($response) {
			$response = new CurlResponse($response);
		} else {
			$this->error = curl_errno($this->request).' - '.curl_error($this->request);
		}
		curl_close($this->request);

		return $response;
	}

	/**
	 * Formats and adds custom headers to the current request
	 *
	 * @return void
	 * @access protected
	 **/
	protected function set_request_headers() {
		$headers = array();
		foreach ($this->headers as $key => $value) {
			$headers[] = $key.': '.$value;
		}
		curl_setopt($this->request, CURLOPT_HTTPHEADER, $headers);
	}

	/**
	 * Set the associated CURL options for a request method
	 *
	 * @param string $method
	 * @return void
	 * @access protected
	 **/
	protected function set_request_method($method) {
		switch (strtoupper($method)) {
			case 'HEAD':
				curl_setopt($this->request, CURLOPT_NOBODY, true);
				break;
			case 'GET':
				curl_setopt($this->request, CURLOPT_HTTPGET, true);
				break;
			case 'POST':
				curl_setopt($this->request, CURLOPT_POST, true);
				break;
			default:
				curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, $method);
		}
	}

	/**
	 * Sets the CURLOPT options for the current request
	 *
	 * @param string $url
	 * @param string $vars
	 * @return void
	 * @access protected
	 **/
	protected function set_request_options($url, $vars) {
		curl_setopt($this->request, CURLOPT_URL, $url);
		if (!empty($vars)) curl_setopt($this->request, CURLOPT_POSTFIELDS, $vars);

		# Set some default CURL options
		curl_setopt($this->request, CURLOPT_HEADER, true);
		curl_setopt($this->request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->request, CURLOPT_USERAGENT, $this->user_agent);
		if ($this->cookie_file) {
			curl_setopt($this->request, CURLOPT_COOKIEFILE, $this->cookie_file);
			curl_setopt($this->request, CURLOPT_COOKIEJAR, $this->cookie_file);
		}
		if ($this->follow_redirects) curl_setopt($this->request, CURLOPT_FOLLOWLOCATION, true);
		if ($this->referer) curl_setopt($this->request, CURLOPT_REFERER, $this->referer);

		# Set any custom CURL options
		foreach ($this->options as $option => $value) {
			curl_setopt($this->request, constant('CURLOPT_'.str_replace('CURLOPT_', '', strtoupper($option))), $value);
		}
	}

}

/**
 * Parses the response from a Curl request into an object containing
* the response body and an associative array of headers
*
* @package curl
* @author Sean Huber <shuber@huberry.com>
**/
class CurlResponse {

	/**
	 * The body of the response without the headers block
	 *
	 * @var string
	 **/
	public $body = '';

	/**
	 * An associative array containing the response's headers
	 *
	 * @var array
	 **/
	public $headers = array();

	/**
	 * Accepts the result of a curl request as a string
	 *
	 * <code>
	 * $response = new CurlResponse(curl_exec($curl_handle));
	 * echo $response->body;
	 * echo $response->headers['Status'];
	 * </code>
	 *
	 * @param string $response
	**/
	function __construct($response) {
		# Headers regex
		$pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';

		# Extract headers from response
		preg_match_all($pattern, $response, $matches);
		$headers_string = array_pop($matches[0]);
		$headers = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));

		# Remove headers from the response body
		$this->body = str_replace($headers_string, '', $response);

		# Extract the version and status from the first header
		$version_and_status = array_shift($headers);
		preg_match_all('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*?)#', $version_and_status, $matches);
		$lastIndex = count($matches[0]) -1;

		$this->headers['Http-Version'] = $matches[1][$lastIndex];
		$this->headers['Status-Code'] = $matches[2][$lastIndex];
		$this->headers['Status'] = $matches[2][$lastIndex].' '.$matches[3][$lastIndex];

		# Convert headers into an associative array
		foreach ($headers as $header) {
			preg_match('#(.*?)\:\s(.*)#', $header, $matches);
			$this->headers[$matches[1]] = $matches[2];
		}
	}

	/**
	 * Returns the response body
	 *
	 * <code>
	 * $curl = new Curl;
	 * $response = $curl->get('google.com');
	 * echo $response;  # => echo $response->body;
	 * </code>
	 *
	 * @return string
	 **/
	function __toString() {
		return $this->body;
	}

}