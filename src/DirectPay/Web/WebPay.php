<?php
namespace ddliu\alipay\DirectPay\Web;
use ddliu\alipay\DirectPay\DirectPayBase;
use ddliu\alipay\DirectPay\PayHelper;

class WebPay extends DirectPayBase {
    const GATEWAY = 'https://mapi.alipay.com/gateway.do?';
    const SERVICE = 'create_direct_pay_by_user';

    protected function getDefaultOptions() {
        return array_merge(parent::getDefaultOptions(), array(
            'service' => self::SERVICE,
        ));
    }

    /**
     * 生成支付url
     */
    public function generatePaymentUrl(array $data) {
        $data = array_merge($this->getDefaultPaymentData(), $data);

        $para = $this->buildRequestPara($data);

        return self::GATEWAY.PayHelper::createLinkstringUrlencode($para);
    }
}