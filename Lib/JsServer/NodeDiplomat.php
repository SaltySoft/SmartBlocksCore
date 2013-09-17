<?php
/**
 * User: antoine.jackson
 * Date: 15/11/12
 * Time: 17:59
 * This class deals with the node server
 */
class NodeDiplomat
{
    public static $port = NODE_PORT;

    public static function sendMessage($session_id, $array)
    {
        $data = array(
            "target_session_id" => $session_id,
            "data" => $array
        );
        return self::post_to_url("http://".$_SERVER["SERVER_NAME"].":".self::$port."/send", $data);

    }

    private static function post_to_url($url, $data)
    {
        $fields = '';

        foreach ($data as $key => $value)
        {
            if (is_array($value))
            {
                $v = urlencode(json_encode($value));
            }
            else
                $v = urlencode($value);
            $fields .= $key . "=" . $v . '&';
        }

        rtrim($fields, '&');

        $post = curl_init();

        curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_POST, count($data));
        curl_setopt($post, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($post);
        return $result;
    }
}
