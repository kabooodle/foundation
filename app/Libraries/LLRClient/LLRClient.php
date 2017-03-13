<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\LLRClient;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Class LLRClient
 * @package Kabooodle\Libraries\LLRClient
 */
class LLRClient extends Client
{
    /**
     * @var array
     */
    protected $connectionError = [];

    /**
     * @param        $code
     * @param string $msg
     */
    public function setConnectionError($code, $msg = '')
    {
        $this->connectionError = [$code => $msg ? : $this->mapResponseCodeToString($code)];
    }

    /**
     * @return array
     */
    public function getConnectionError()
    {
        return $this->connectionError;
    }

    /**
     * @param LLRCredentials $credentials
     *
     * @return array|bool
     */
    public function login(LLRCredentials $credentials)
    {
        $jar = new CookieJar(false, $credentials->getCookie());

        try {
            $response = $this->request('POST', LLRRoutes::ROUTE_LOGIN_POST, [
                'form_params' => [
                    'email' => $credentials->getEmail(),
                    'password' => $credentials->getPassword(),
                    '_token'
                ],
                'cookies' => $jar,
                'allow_redirects' => false
            ]);

            if (! in_array($response->getStatusCode(), [302, 200])) {
                $this->setConnectionError($response->getStatusCode());

                return false;
            }

            return [$this, $response, $jar];
        } catch (RequestException $e) {
            $this->setConnectionError($e->getCode());

            return false;
        }
    }

    public function getResponseCookie(ResponseInterface $promise)
    {
        //
    }

    /**
     * @param int $code
     *
     * @return string
     */
    public function mapResponseCodeToString($code)
    {
        $codes = $this->getResponseCodesList();

        return $codes[(int) $code];
    }

    /**
     * @return array
     */
    public function getResponseCodesList()
    {
        return [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            419 => "Authentication Timeout",
            420 => "Enhance Your Calm",
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            428 => "Precondition Required",
            429 => "Too Many Requests",
            431 => "Request Header Fields Too Large",
            444 => "No Response",
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            451 => "Unavailable For Legal Reasons",
            494 => "Request Header Too Large",
            495 => "Cert Error",
            496 => "No Cert",
            497 => "HTTP to HTTPS",
            499 => "Client Closed Request",
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended',
            511 => "Network Authentication Required",
            598 => "Network read timeout error",
            599 => "Network connect timeout error"
        ];
    }
}
