<?php
namespace ddliu\alipay\DirectPay\Mobile;
use ddliu\alipay\DirectPay\DirectPayBase;
use ddliu\alipay\DirectPay\PayHelper;

class MobilePay extends DirectPayBase {
    const SERVICE = 'mobile.securitypay.pay';

    protected function getDefaultOptions() {
        return array_merge(parent::getDefaultOptions(), array(
            'service' => self::SERVICE
        ));
    }

    /**
     * 生成支付串（移动端使用）
     * @param  array $data 
     *  - notify_url: 可选，覆盖默认notify_url
     *  - out_trade_no: 单号
     *  - subject
     *  - total_fee
     *  - body
     *  - show_url: 可选
     *  - anti_phishing_key: 可选
     *  - exter_invoke_ip: 可选
     * @return string
     */
    public function generatePaymentString(array $data) {
        $data = array_merge($this->getDefaultPaymentData(), $data);
        return PayHelper::createLinkstringUrlencode($this->buildRequestPara($data));
    }
}