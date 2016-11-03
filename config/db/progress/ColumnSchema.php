<?php
namespace exchangecore\yii2\progress\driver\db\progress;

class ColumnSchema extends \yii\db\ColumnSchema
{
    /**
     * set to false if value is not case sensitive, set to CASE_UPPER for conversion to upper case, set to CASE_LOWER
     * for conversion to lower case
     */
    public $case = CASE_UPPER;

    protected function typecast($value)
    {
        $value = parent::typecast($value);
        if ($this->case !== false && is_string($value)) {
            if($this->case === CASE_LOWER) {
                $value = strtolower($value);
            } elseif ($this->case === CASE_UPPER) {
                $value = strtoupper($value);
            }
        }
        return utf8_encode($value);
    }
} 
