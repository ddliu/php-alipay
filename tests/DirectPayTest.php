<?php
use ddliu\alipay\DirectPay\Mobile\MobilePay;
use ddliu\alipay\DirectPay\Web\WebPay;
class DirectPayTest extends PHPUnit_Framework_TestCase {
    protected $productData = array(
        'out_trade_no' => '123',
        'subject' => 'Macbook Pro',
        'total_fee' => '10000',
        'body' => '',
    );

    protected $options = array(
        'partner' => 'partner',
        'seller_id' => '123',
        'key' => '123',
        'notify_url' => 'http://mall.test.com/notify/alipay',
        'sign_type' => 'MD5',
    );

    protected function getMobileClient() {
        return new MobilePay($this->options);
    }

    protected function getWebClient() {
        return new WebPay($this->options);
    }

    public function testWebGenerateUrl() {
        $url = $this->getWebClient()->generatePaymentUrl($this->productData);
        echo $url.PHP_EOL;
    }

    public function testMobileGenerateString() {
        $url = $this->getMobileClient()->generatePaymentString($this->productData);
        echo $url.PHP_EOL;
    }
}