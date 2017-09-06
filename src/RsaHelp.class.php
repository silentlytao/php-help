<?php
namespace Common\Help;
/**
 * 基础框架-助手类-RSA加密，解密
 * 需要开启Open_ssl扩展
 */
class RsaHelp extends BaseHelp
{
    /** 私钥*/
    private static $PRIVATE_KEY;
    /** 公钥*/
    private static $PUBLIC_KEY;
    /**
     * 构造函数
     * @param string $pubKey 公钥
     * @param string $privKey 私钥
     */
    function __construct($pubKey = '', $privKey = '')
    {
        self::$PUBLIC_KEY = $pubKey;
        self::$PRIVATE_KEY = $privKey;
    }

    /**
     * Decode a string with URL-safe Base64.
     *
     * @param string $input
     *            A Base64 encoded string
     *            
     * @return string A decoded string
     */
    public static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * Encode a string with URL-safe Base64.
     *
     * @param string $input
     *            The string you want encoded
     *            
     * @return string The base64 encode of what you passed in
     */
    public static function urlsafeB64Encode($input)
    {
        return base64_encode($input);
        // return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * 返回对应的私钥(内部类调用)
     */
    private static function getPrivateKey()
    {
        $privKey = self::$PRIVATE_KEY;
        return openssl_pkey_get_private($privKey);
    }

    /**
     * 返回对应的公钥(内部类调用)
     */
    private static function getPublicKey()
    {
        $pubKey = self::$PUBLIC_KEY;
        return openssl_pkey_get_public($pubKey);
    }

    /**
     * 私钥加密
     * @param array $data 
     */
    public static function privEncrypt($data)
    {
        if (! is_string($data)) {
            return null;
        }
        return openssl_private_encrypt($data, $encrypted, self::getPrivateKey()) ? self::urlsafeB64Encode($encrypted) : null;
    }

    /**
     * 私钥解密
     * @param array $encrypted 
     */
    public static function privDecrypt($encrypted)
    {
        if (! is_string($encrypted)) {
            return null;
        }
        return (openssl_private_decrypt(self::urlsafeB64Decode($encrypted), $decrypted, self::getPrivateKey())) ? $decrypted : null;
    }

    /**
     * 公钥加密
     * @param array $data 
     */
    public static function pubEncrypt($data)
    {
        if (! is_string($data)) {
            return null;
        }
        return openssl_public_encrypt($data, $encrypted, self::getPublicKey()) ? self::urlsafeB64Encode($encrypted) : null;
    }

    /**
     * 公钥解密
     * @param array $encrypted 
     */
    public static function pubDecrypt($encrypted)
    {
        if (! is_string($encrypted)) {
            return null;
        }
        return (openssl_public_decrypt(self::urlsafeB64Decode($encrypted), $decrypted, self::getPublicKey())) ? $decrypted : null;
    }
}
?>