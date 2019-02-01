<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 // require_once APPPATH . '/libraries/REST_Controller.php';

// require_once APPPATH . 'libraries/REST_Controller.php';
// require APPPATH . 'libraries/JWT.php';
// require APPPATH . 'libraries/BeforeValidException.php';
// require APPPATH . 'libraries/ExpiredException.php';
// require APPPATH . 'libraries/SignatureInvalidException.php';
// use \Firebase\JWT\JWT;

require_once APPPATH . 'libraries/REST_Controller.php';
require APPPATH . '/libraries/jwt/JWT.php';
require APPPATH . '/libraries/jwt/BeforeValidException.php';
require APPPATH . '/libraries/jwt/ExpiredException.php';
require APPPATH . '/libraries/jwt/SignatureInvalidException.php';
use \Firebase\JWT\JWT;

// use Restserver\Libraries\REST_Controller;

class MY_Controller extends REST_Controller {

    protected $secretkey = 'jeim5083';

     // Informational

    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;            // RFC2518

    // Success

    /**
     * The request has succeeded
     */
    const HTTP_OK = 200;

    /**
     * The server successfully created a new resource
     */
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;

    /**
     * The server successfully processed the request, though no content is returned
     */
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;          // RFC4918
    const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    const HTTP_IM_USED = 226;               // RFC3229

    // Redirection

    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;

    /**
     * The resource has not been modified since the last request
     */
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_RESERVED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238

    // Client Error

    /**
     * The request cannot be fulfilled due to multiple errors
     */
    const HTTP_BAD_REQUEST = 400;

    /**
     * The user is unauthorized to access the requested resource
     */
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;

    /**
     * The requested resource is unavailable at this present time
     */
    const HTTP_FORBIDDEN = 403;

    /**
     * The requested resource could not be found
     *
     * Note: This is sometimes used to mask if there was an UNAUTHORIZED (401) or
     * FORBIDDEN (403) error, for security reasons
     */
    const HTTP_NOT_FOUND = 404;

    /**
     * The request method is not supported by the following resource
     */
    const HTTP_METHOD_NOT_ALLOWED = 405;

    /**
     * The request was not acceptable
     */
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;

    /**
     * The request could not be completed due to a conflict with the current state
     * of the resource
     */
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    const HTTP_LOCKED = 423;                                                      // RFC4918
    const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;   // RFC2817
    const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585

    // Server Error

    /**
     * The server encountered an unexpected error
     *
     * Note: This is a generic error message when no specific message
     * is suitable
     */
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * The server does not recognise the request method
     */
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;

    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
    }

    public function response1($message, $status, $data=null){
        $output['status'] = $status;
        $output['message'] = $message;
        if ($data && $status==200) {
            $output['data'] = $data;
        }else{
        	log_message('error', "$status ->>> $message");
        }
        return json_encode($output);
    }

    public function response2($message, $status, $data=null){
        $output['status'] = $status;
        $output['message'] = $message;
        if ($data && $status==200) {

            $output['data']['count'] = count($data);
            $output['data']['list'] = $data;
        }else{
            log_message('error', "$status ->>> $message");
        }
        return json_encode($output);
    }

    public function validasi($token, $validate = TRUE){

        try {

            $decoded = JWT::decode($token,$this->secretkey,array('HS256'));
            $date1 = new DateTime(date('Y-m-d H:i:s', $decoded->exp));
            $date2 = new DateTime("now");

            if ($decoded) {
                if ($validate) {
                    if ( date('Y-m-d H:i:s',$decoded->exp) > date('Y-m-d H:i:s',$date2->format('U')) ) {
                         return true;
                    }else{
                        echo $this->response('Token_Expired', 401);
                        exit;
                    }
                }else{
                    return true;
                }
            }else{
                echo $this->response('Invalid_Token_Request', 401);
                exit;
            }

        } catch (Exception $e) {
            return false;
        }

    }

    public function payload($token){

        try {

            $decoded = JWT::decode($token,$this->secretkey,array('HS256'));
            if ($decoded) {
                return $decoded;
            }else{
                $output['status'] = 402;
                $output['message'] = 'Token Invalid';
                echo $this->response($output['message'], $output['status']);
                exit;

            }

        } catch (Exception $e) {
            $output['status'] = 402;
            $output['message'] = 'Token Invalid';
            echo $this->response($output['message'], $output['status']);
            exit;
        }

    }

    public function generateNewToken($credential){
        $output = JWT::encode($credential,$this->secretkey);
        return $output;
    }

}
