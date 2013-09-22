<?php
/**
 * Copyright (C) 2013 Antoine Jackson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 */

require_once (ROOT . DS . "Config" . DS . "database.php");
class PagesController extends Controller
{
    public function security_check()
    {
        if (!User::logged_in())
        {
            $this->redirect("/Users/login_form");
        }
    }

    function home($params = array())
    {
        $this->security_check();
    }

    /**
     * Delete this function once the application is functional (this is just the config check).
     */
    function configuration($params = array())
    {
        $this->security_check();

        $db_config = new DbConfig;
        $dsn = 'mysql:host=' . $db_config->dev["host"] . ';port=3306;dbname=' . $db_config->dev["database"] . '';
        $this->set("database_status", true);
        try
        {
            $dbh = new PDO($dsn, $db_config->dev["user"], $db_config->dev["password"]);
        } catch (PDOException $exception)
        {
            $this->set("database_status", false);
        }

        if (extension_loaded("apc"))
        {
            $this->set("apc_set", true);
        }
        else
        {
            $this->set("apc_set", false);
        }

    }

    function e404($params = array())
    {
    }

    function e500($params = array())
    {
    }

    function contact($params = array())
    {
    }

    function list_texts($params = array())
    {
    }

    function block_setup($params = array())
    {
    }

    function setLocale($params = array())
    {
        if (isset($params["locale"]))
        {
            Intl::setLocale($params["locale"]);
        }
        $this->flash("Language changed to " . Intl::getLocale());
        $this->redirect("/");
    }

    function gocontact($params = array())
    {
        $this->flash("sup");
        $this->redirect("/contact");
    }

    function landing_page($params = array())
    {
        $this->render_layout = false;
    }

    private function _formatOauthReq($OAuthParams, $scope)
    {
        $uri = $OAuthParams['oauth_uri'];
        $uri .= "?client_id=" . $OAuthParams['client_id'];
        $uri .= "&redirect_uri=" . $OAuthParams['redirect_uri'];
        $uri .= "&scope=" . $scope;
        $uri .= "&response_type=code";
        $uri .= "&access_type=offline";

        return $uri;
    }

    private function _get_url_param($url, $name)
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        return isset($params[$name]) ? $params[$name] : null;
    }

    private function _get_auth_token($params, $code)
    {
        $url = $params['oauth_token_uri'];

        $fields = array(
            'code' => $code,
            'client_id' => $params['client_id'],
            'client_secret' => $params['client_secret'],
            'redirect_uri' => $params['redirect_uri'],
            'grant_type' => 'authorization_code'
        );
        $response = $this->_do_post($url, $fields);
        echo " heeyy " . $response . " heeyy ";
        return $response;
    }

    private function _do_post($url, $fields)
    {
//        $fields_string = '';
//
//        foreach ($fields as $key => $value)
//        {
//            $fields_string .= $key . '=' . $value . '&';
//        }
//        $fields_string = rtrim($fields_string, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function googleoauth($params = array())
    {
        $this->render = false;
        $OAuth = array(
            'oauth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'client_id' => '850249449787-ord751rqtpuc7nkoj4gid81n8j3589cn.apps.googleusercontent.com',
            'redirect_uri' => 'http://localhost:8080/Pages/googleoauth', //insert your redirect uri here
            'client_secret' => 'q7bWo_lAEVJvxLXd7oLwkLnB',
            'oauth_token_uri' => 'https://accounts.google.com/o/oauth2/token'
        );

        $token = array(
            'access_token' => '',
            'token_type' => '',
            'expires_in' => '',
            'refresh_token' => ''
        );


        $error = $this->_get_url_param($_SERVER['REQUEST_URI'], 'error');
        if ($error != NULL)
        { // this means the user denied api access to GWMTs
            echo $error;
        }
        else
        {
            $AuthCode = $this->_get_url_param($_SERVER['REQUEST_URI'], 'code');
            if ($AuthCode == NULL)
            { // get authorization code
                $OAuth_request = $this->_formatOAuthReq($OAuth,
                    "https://www.google.com/calendar/feeds/");

                header('Location: ' . $OAuth_request);
                exit; // the redirect will come back to this page and $code will have a value
            }
            else
            {
                echo 'Got Authorization Code : ' . $AuthCode;
                $token_response = $this->_get_auth_token($OAuth, $AuthCode);
                $json_obj = json_decode($token_response);
                $token['access_token'] = $json_obj->access_token;
                $token['token_type'] = $json_obj->token_type;
                $token['expires_in'] = $json_obj->expires_in;
                $token['refresh_token'] = isset($json_obj->refresh_token) ? $json_obj->refresh_token : "none";
                echo 'access_token = ' . $json_obj->access_token;
                echo "<br/>refresh" . $token['refresh_token'];
                echo "<br/>expires" . $token['expires_in'];
                echo "<br/>token_type" .   $token['token_type'];
            }
        }

    }
}
