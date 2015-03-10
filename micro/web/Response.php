<?php /** MicroResponse */

namespace Micro\web;

/**
 * Response class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
 * @subpackage web
 * @version 1.0
 * @since 1.0
 */
class Response
{
    /** @var string $httpVersion Protocol version */
    protected $httpVersion = 'HTTP/1.1';
    /** @var int $statusCode Code for status of operation */
    protected $statusCode = 200;
    /** @var string $statusMessage Message for status of operation */
    protected $statusMessage = 'OK';
    /** @var string $contentType Content type of result */
    protected $contentType = 'Content-Type: text/html';
    /** @var array $headers Headers of response */
    protected $headers = [];
    /** @var mixed $body Body of response */
    protected $body = false;


    /**
     * Create and initialize response
     *
     * @access public
     *
     * @param string  $body response body
     * @param int    $status HTTP status code, default 200
     * @param string $message HTTP status message, default OK
     * @param array  $headers HTTP headers
     *
     * @result void
     */
    public function __construct( $body = '', $status = 200, $message = null, array $headers = [] )
    {
        $this->setStatus($status, $message);
        $this->setHeaders($headers);
        $this->setBody($body);
    }

    /**
     * Add header into headers array
     *
     * @access public
     *
     * @param string $name Name of header
     * @param string $value Value header
     * @param bool   $replace Replaced if exists?
     *
     * @return void
     */
    public function addHeader( $name, $value, $replace = true )
    {
        $this->headers[$name] = $replace ? $value : ( empty($this->headers[$name]) ? $value : $this->headers[$name] );
    }

    /**
     * Get header if defined
     *
     * @access public
     *
     * @param string $name Name of header to get
     *
     * @return string|null
     */
    public function getHeader( $name )
    {
        return !empty($this->headers) ? $this->headers[$name] : NULL;
    }

    /**
     * Set HTTP version
     *
     * @access public
     *
     * @param string $version
     *
     * @return void
     */
    public function setHttpVersion( $version = 'HTTP/1.1' )
    {
        $this->httpVersion = $version;
    }

    /**
     * Resets all headers
     *
     * @access public
     *
     * @param array $headers New headers array
     *
     * @return void
     */
    public function setHeaders( array $headers = [] )
    {
        $this->headers = $headers;
    }

    /**
     * Set status for response
     *
     * @access public
     *
     * @param int         $status Code for a new status
     * @param string|null $message Message for a new status
     *
     * @return void
     */
    public function setStatus( $status = 200, $message = null )
    {
        $this->statusCode = $status;
        $this->setStatusMessage($message);
    }

    /**
     * Set HTTP status message
     *
     * @access public
     *
     * @param string $message New message
     *
     * @return void
     */
    public function setStatusMessage( $message = '' )
    {
        $this->statusMessage = !empty($message) ? $message : $this->getStatusMessageFromCode($this->statusCode);
    }

    /**
     * Get HTTP status message from HTTP status code
     *
     * @access public
     *
     * @param int $code Code for get message
     *
     * @return string
     */
    public function getStatusMessageFromCode( $code = 200 )
    {
        $codes = [
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            503 => 'Service Unavailable',
            // @TODO: add other elements
        ];
        return !empty($codes[$code]) ? $codes[$code] : '';
    }

    /**
     * Set content type of HTTP body
     *
     * @access public
     *
     * @param string $newType New HTTP content type
     *
     * @return void
     */
    public function setContentType( $newType = '' )
    {
        $this->contentType = !empty($newType) ? $newType : 'Content-Type: text/html';
    }

    /**
     * Set body for response
     *
     * @access public
     *
     * @param string $data Data for HTTP response body
     *
     * @return void
     */
    public function setBody($data = '')
    {
        $this->body = $data;
    }

    /**
     * Send headers into browser
     *
     * @access public
     *
     * @return void
     */
    public function sendHeaders()
    {
        $message = $this->statusMessage ?: $this->getStatusMessageFromCode($this->statusCode);
        header($this->httpVersion . ' ' . $this->statusCode . ' ' . $message);

        foreach ($this->headers AS $key => $val) {
            header($key . ': ' . $val);
        }

    }

    /**
     * Return current body
     *
     * @access public
     *
     * @return string
     */
    public function sendBody()
    {
        return $this->body;
    }

    /**
     * Public convert response to string (for send to client) and send headers
     *
     * @access public
     *
     * @return string
     */
    public function __toString()
    {
        $this->sendHeaders();
        return $this->sendBody();
    }
}