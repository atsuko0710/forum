<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spam extends Model
{
    /**
     * 关键字检测
     *
     * @param string $body
     * @return void
     */
    public function detect($body)
    {
        $this->detectInvalidKeywords($body);
        return false;
    }

    /**
     * 关键字检测逻辑
     *
     * @param string $body
     * @return void
     */
    public function detectInvalidKeywords($body)
    {
        $invalidKeywords = [
            'forbidden'
        ];

        foreach ($invalidKeywords as $invalidKeyword) {
            if (stripos($body, $invalidKeyword) !== false) {
                throw new \Exception("含有敏感词");
            }
        }
    }
}