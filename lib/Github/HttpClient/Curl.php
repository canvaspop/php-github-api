<?php

/**
 * Performs requests on GitHub API. API documentation should be self-explanatory.
 *
 * @author    Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @license   MIT License
 */
class Github_HttpClient_Curl
{
    /**
     * Send a request to the server, receive a response
     *
     * @param  string   $url           Request url
     * @param  array    $parameters    Parameters
     * @param  string   $httpMethod    HTTP method to use
     * @param  array    $options        Request options
     *
     * @return string   HTTP response
     */
    public function doSend($url, array $parameters = array(), $httpMethod = 'GET', array $options)
    {
        $url = strtr($options['url'], array(
            ':protocol' => $options['protocol'],
            ':format'   => $options['format'],
            ':path'     => trim($url, '/')
        ));

        $curlOptions = array();

        if ($options['login']) {
            switch ($options['auth_method']) {
                case phpGitHubApi::AUTH_HTTP_PASSWORD:
                    $curlOptions += array(
                        CURLOPT_USERPWD => $options['login'].':'.$options['secret'],
                    );
                    break;
                case phpGitHubApi::AUTH_HTTP_TOKEN:
                    $curlOptions += array(
                        CURLOPT_USERPWD => $options['login'].'/token:'.$options['secret'],
                    );
                    break;
                case phpGitHubApi::AUTH_URL_TOKEN:
                default:
                    $parameters = array_merge(array(
                        'login' => $options['login'],
                        'token' => $options['secret']
                            ), $parameters);
                    break;
            }
        }

        if (!empty($parameters)) {
            $queryString = utf8_encode(http_build_query($parameters, '', '&'));

            if ('GET' === $httpMethod) {
                $url .= '?'.$queryString;
            } else {
                $curlOptions += array(
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $queryString
                );
            }
        }

        $this->debug('send '.$httpMethod.' request: '.$url);

        $curlOptions += array(
            CURLOPT_URL => $url,
            CURLOPT_PORT => $options['http_port'],
            CURLOPT_USERAGENT => $options['user_agent'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $options['timeout']
        );

        $curl = curl_init();

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);

        if (!in_array($headers['http_code'], array(0, 200, 201))) {
            throw new Github_HttpClient_Exception(null, (int) $headers['http_code']);
        }

        if ($errorNumber != '') {
            throw new Github_HttpClient_Exception('error '.$errorNumber);
        }

        return $response;
    }
}