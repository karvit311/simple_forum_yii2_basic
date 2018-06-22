<?php

namespace app\modules\forum\validators;

use yii;
use app\modules\forum\models\IgnoreListEntry;
use yii\validators\Validator;

/**
 * IgnoreListValidator
 *
 * Ensure a recipient is not in the ignore list. If he is, avoid the message transmission.
 */
class IgnoreListValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $entry = IgnoreListEntry::find()->where(['user_id' => $model->to, 'blocks_user_id' => $model->from])->one();

        if ($entry)
            $this->addError($model, 'to', Yii::t(
                'message', 'The recipient has added you to the ignore list. You can not send any messages to this person.'));
    }
}