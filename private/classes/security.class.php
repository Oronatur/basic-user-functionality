<?php
require_once dirname( __DIR__ ) . '/autoload.php';

class Security
{
    public function session_token()
        {
            if(empty($_SESSION['token']))
                {
                    $_SESSION['token'] = bin2hex(random_bytes(32));
                }
        }

    public function verify_token($token)
        {
            if (!isset($token))
                {
                    echo 'No token.';
                    return false;
                }
            elseif(hash_equals($token, $_SESSION['token']) === false)
                {
                    $from = $_SERVER['HTTP_REFERER'];
                    $time = date("H:i:s");
                    $string = 'User ID: ' . $_SESSION['id'] . ' time: ' . $time . ' from: ' . $from . PHP_EOL;
                    $fp = fopen('/private/Security_log/log.txt', 'a');
                    fwrite($fp, 'Token mismatch ' . $string);
                    fclose($fp);

                    echo 'Token mismatch.';
                    return false;
                }
            elseif(hash_equals($token, $_SESSION['token']) === true)
                {
                    return true;
                }
            else
                {
                    exit('Error.');
                }
            }

    public function specialchars($string)
        {
            return htmlspecialchars("$string", ENT_QUOTES, 'UTF-8');
        }
}

?>