<?php

namespace App\Inspections;

/**
 * 是否包含关键字检测
 */
class InvalidKeywords
{
    protected $invalidKeywords = [
        'forbidden'
    ];

    public function detect($body)
    {
        foreach ($this->invalidKeywords as $invalidKeyword) {
            if (stripos($body, $invalidKeyword) !== false) {
                throw new \Exception("含有敏感词");
            }
        }
    }
}