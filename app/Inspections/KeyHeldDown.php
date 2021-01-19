<?php

namespace App\Inspections;

/**
 * 输入文本是否重复
 */
class KeyHeldDown
{
    public function detect($body)
    {
        // \\1 指的一个单词
        if (preg_match('/(.)\\1{4,}/', $body)) {
            throw new \Exception("含有重复信息");
        }
    }
}