<?php

namespace App\Model;

class jsonConverterModel extends Model {

    public static function encode ($data) {
        if (is_string($data)) {
            $data = str_replace(
                    array('\\', '/', '"', "\r", "\n", "\b", "\f", "\t"),
                    array('\\\\', '\/', '\"', '\r', '\n', '\b', '\f', '\t'),
                    $data
            );

            $convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
            $result = '';

            for ($i = mb_strlen($data) - 1; $i >= 0; $i--) {
                $result = mb_substr($data, $i, 1) . $result;
            }

            return '"' . $result . '"';
        } elseif (is_array($data)) {
            $with_keys = false;
            $n = count($data);

            for ($i = 0, reset($data); $i < $n; $i++, next($data)) {
                if (key($data) !== $i) {
                    $with_keys = true;
                    break;
                }
            }
        } else {
            return '';
        }

        $result = array();

        if ($with_keys) {
            foreach ($data as $key => $v) {
                $result[] = self::encode((string)$key) . ':' . self::encode($v);    
            }

            return '{' . implode(',', $result) . '}';                
        } else {
            foreach ($data as $key => $v) {
                $result[] = self::encode($v);    
            }

            return '[' . implode(',', $result) . ']';
        }
    }
}