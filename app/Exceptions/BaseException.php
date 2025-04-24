<?php
namespace App\Exceptions;

// use Illuminate\Support\MessageBag;

abstract class BaseException extends \Exception
{
    protected const DEFAULT_MESSAGE = "伺服器忙碌中";

    /**
     * @var $subErrorCode
     */
    private $subErrorCode;

    protected $optionalData;

    public function __construct($subErrorCode, $optionalData = [])
    {
        $this->subErrorCode = $subErrorCode;
        $this->optionalData = $optionalData;

        $class = get_class($this);
        $baseCode = (new ExceptionCodeManager)->getBaseCode($class);

        parent::__construct($this->getErrorMessage(), $baseCode + $subErrorCode);
    }

    /**
     * 定義給客戶看的訊息
     * 回傳
     * key 為 sub error code
     * value 為對應訊息的陣列
     * 例：
     * [
     *     1 => '伺服器錯誤',
     *     2 => '餘額不足',
     * ]
     * @return array
     */
    abstract protected function getClientMessage();

    /**
     * 取得給前端看的訊息
     * 若為空預設為 伺服器忙碌中
     */
    final public function getErrorMessage()
    {
        return $this->getClientMessage()[$this->getSubErrorCode()] ?? self::DEFAULT_MESSAGE;
    }

    /**
     * 取得debug訊息定義的陣列
     * 格式比照 getClientMessage
     * @return array
     */
    abstract protected function getDebugDefinition();

    /**
     * 取得 dev 環境用的訊息
     */
    public function getDebugMessage() {
        return ($this->getDebugDefinition()[$this->getSubErrorCode()] ?? "") .
            (!empty($this->optionalData) ? json_encode($this->optionalData) : "")
            ;
    }

    /**
     * @return mixed
     */
    protected function getSubErrorCode()
    {
        return $this->subErrorCode;
    }

    /**
     * 取得 客制給client的詳細錯誤訊息
     */
    public function getCustomClientMessage()
    {
        if (!empty($this->optionalData['custom'])) {
            return $this->optionalData['custom'];
        }

        return $this->getErrorMessage();
    }

    /**
     * @param $name
     * @param $arguments
     * @return int|mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public static function __callStatic($name, $arguments)
    {
        $className = static::class;

        if (constant("{$className}::{$name}") !== null) {
            $baseCode = (new ExceptionCodeManager)->getBaseCode($className);
            $oriCode = constant("{$className}::{$name}");
            return $baseCode + (int)$oriCode;
        }

        return call_user_func_array("\\{$className}::{$name}", $arguments);
    }

    public function __toString()
    {
        return get_class($this) . " '{$this->getMessage()}({$this->getCode()})' in {$this->file}({$this->line})\n"
                                . "Debug info:\n"
                                . "{$this->getDebugMessage()}\n"
                                . "Stack trace:\n"
                                . "{$this->getTraceAsString()}";
    }
}
