<?php

if (! function_exists('convertBooleanToBpjsString')) {
    /**
     * Mengubah Null dan Boolean menjadi nilai string.
     *
     * @param  bool|null  $value  Nilai yang ingin dikonversi
     */
    function convertBooleanToBpjsString(?bool $value): string
    {
        if ($value === null) {
            return '';
        }

        return strval(intval($value));
    }
}
