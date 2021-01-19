<?php

namespace App\Inspections;

class Spam
{
    // 检测机制
    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class
    ];

    /**
     * 关键字检测
     *
     * @param string $body
     * @return void
     */
    public function detect($body)
    {
        foreach ($this->inspections as $inspection) {
            app($inspection)->detect($body);
        }
        
        return false;
    }
}