<?php
/**
 * User: martyn ling <mling@orthomeo.com>
 * Date: 05/12/13
 * Time: 23:19
 */

namespace Payum\Yii2Extension\Model;

use Payum\Core\Model\ArrayObject;

class PaymentDetailsActiveRecordWrapper extends ArrayObject
{
    public $activeRecord;

    public function __construct($scenario = 'insert', $tableName = '')
    {
        if ($scenario == 'insert') {
            $this->activeRecord = new PaymentActiveRecord('insert', $tableName);
        }
    }

    public function primaryKey()
    {
        return $this->activeRecord->tableSchema->primaryKey;
    }

    public function save()
    {
        $this->activeRecord->_details = serialize($this->array);
        $this->activeRecord->save();
        //die(print_r($this->primaryKey(), true));
        $this[current($this->primaryKey())] = $this->activeRecord->{current($this->primaryKey())};
    }

    public function delete()
    {
        $this->activeRecord->delete();
    }

    public static function findModelById($tableName, $id)
    {
        $paymentDetails = new PaymentDetailsActiveRecordWrapper('update');
        $paymentDetails->activeRecord = PaymentActiveRecord::model($tableName)->findByPk($id);
        $paymentDetails->refreshFromActiveRecord();

        return $paymentDetails;
    }

    public function refreshFromActiveRecord()
    {
        $this[$this->primaryKey()] = $this->activeRecord->{$this->primaryKey()};
        $this->array = unserialize($this->activeRecord->_details);
    }
}
