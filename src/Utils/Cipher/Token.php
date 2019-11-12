<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils\Cipher;

/**
 * 令牌加密
 * @package Jmhc\Restful\Utils\Cipher
 */
class Token extends Base
{
    /**
     * 填充位置
     * @var int
     */
    protected $pos;

    /**
     * 填充长度
     * @var int
     */
    protected $len;

    /**
     * 初始化
     */
    protected function initialize()
    {
        parent::initialize();

        $this->pos = $this->config['pos'] ?? 5;
        $this->len = $this->config['pos'] ?? 6;
    }

    /**
     * 加密字符串
     * @param string $str
     * @return string
     */
    public function encrypt(string $str)
    {
        return rawurlencode($this->encryptStr($str));
    }

    /**
     * 解密字符串
     * @param  string $str
     * @return string
     */
    public function decrypt(string $str)
    {
        return $this->decryptStr(rawurldecode($str));
    }

    /**
     * 验证token
     * @param  string $str   需加密字符串
     * @param  string $token 加密后的字符串
     * @return boolean
     */
    public function verify(string $str, string $token)
    {
        return $str == $this->decryptStr($token);
    }

    /**
     * 加密字符串
     * @param  string $str
     * @return string
     */
    private function encryptStr(string $str)
    {
        $fill = base64_encode(md5(uniqid()));
        $fill = substr($fill, 0, $this->len);
        $en   = openssl_encrypt($str . $fill, $this->method, $this->key, 0, $this->iv);
        $ens  = substr($en, 0, $this->pos) . $fill . substr($en, $this->pos);
        return $ens;
    }

    /**
     * 解密字符串
     * @param  string $str
     * @return string
     */
    private function decryptStr(string $str)
    {
        $str = substr($str, 0, $this->pos) . substr($str, $this->pos + $this->len);
        $de  = openssl_decrypt($str, $this->method, $this->key, 0, $this->iv);
        $des = substr($de, 0, - $this->len);
        return (string) $des;
    }
}
