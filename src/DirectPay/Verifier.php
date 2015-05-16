<?php
namespace ddliu\alipay\DirectPay;

class Verifier {
    CONST HTTPS_VERIFY_URL = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    CONST HTTP_VERIFY_URL  = 'http://notify.alipay.com/trade/notify_query.do?';

    /**
     * 配置选项
     * @var array
     *  - 
     */
    protected $options;

    public function __construct(array $options) {
        $this->options = $options;
    }

    public function getOption($key, $default = null) {
        return isset($this->options[$key])?$this->options[$key]:$default;
    }

    public function verify($data) {
        if (!$data) {
            return false;
        }

        if (empty($data['notify_id'])) {
            return false;
        }

        // 生成签名结果
        if (!$this->getSignVeryfy($data, $data['sign'])) {
            return false;
        }

        // 获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $response_txt = $this->getResponse($data['notify_id']);

        // 验证
        // $response_txt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        // isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        if (preg_match('/true$/i', $response_txt)) {
            unset($data['sign']);
            return $data;
        } else {
            return false;
        }
    }

    public function verifyRequest() {
        return $this->verify($_SERVER['REQUEST_METHOD'] === 'POST'?$_POST:$_GET);
    }

    public function verifyPost() {
        return $this->verify($_POST);
    }

    public function verifyGet() {
        return $this->verify($_GET);
    }

    public function confirm() {
        echo 'success';
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    function getSignVeryfy($para_temp, $sign)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = PayHelper::paraFilter($para_temp);
        //对待签名参数数组排序
        $para_sort = PayHelper::argSort($para_filter);
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = PayHelper::createLinkstring($para_sort);
        $is_sgin = false;
        switch (strtoupper($this->getOption('sign_type'))) {
            case 'MD5':
                $is_sgin = PayHelper::md5Verify($prestr, $sign, $this->getOption('key'));
                break;
            case 'RSA':
                $is_sgin = PayHelper::rsaVerify($prestr, $this->getOption('public_key_path'), $sign);
                break;
            default:
                $is_sgin = false;
        }
        return $is_sgin;
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    private function getResponse($notify_id)
    {
        $transport = strtolower($this->getOption('transport'));
        if ($transport == 'https') {
            $veryfy_url = self::HTTPS_VERIFY_URL;
        } else {
            $veryfy_url = self::HTTP_VERIFY_URL;
        }
        $veryfy_url .= 'partner=' . $this->getOption('partner') . '&notify_id=' . $notify_id;
        $response_txt = $this->getHttpResponseGET($veryfy_url, $this->getOption('cacert'));

        return $response_txt;
    }

    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert 指定当前工作目录绝对路径
     * return 远程输出的数据
     */
    private function getHttpResponseGET($url, $cacert)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); //SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //严格认证
        curl_setopt($curl, CURLOPT_CAINFO, $cacert); //证书地址
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

        return $responseText;
    }
}