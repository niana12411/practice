<?php

namespace App\Exceptions;

use Illuminate\Support\MessageBag;

class OpenweathermapException extends BaseException
{
    const CITY_ID_NOT_EXIST = 1;
    const CURL_REQUEST_ERROR = 2;
    const HTTP_CODE_ERROR = 3;
    const RD_CODE_ERROR = 4;
    const RESPONSE_CODE_ERROR = 5;
    const SETTING_KEY_NOT_FOUND = 6;

    protected function getClientMessage(): array
    {
        return [
            self::CITY_ID_NOT_EXIST => '查無地點',
            self::CURL_REQUEST_ERROR => '呼叫api異常',
            self::HTTP_CODE_ERROR => '查詢異常',
            self::RD_CODE_ERROR => '查詢異常',
            self::RESPONSE_CODE_ERROR => '查無對應訊息',
            self::SETTING_KEY_NOT_FOUND => '未設定參數',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getDebugDefinition(): array
    {
        return [
            self::CITY_ID_NOT_EXIST => '查無地點',
            self::CURL_REQUEST_ERROR => '呼叫api異常',
            self::HTTP_CODE_ERROR => 'http code 異常',
            self::RD_CODE_ERROR => 'rd code 異常',
            self::RESPONSE_CODE_ERROR => '查無對應訊息',
            self::SETTING_KEY_NOT_FOUND => '未設定參數',
        ];
    }
}
