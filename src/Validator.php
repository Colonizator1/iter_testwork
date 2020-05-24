<?php

namespace Iter;

class Validator
{
    public function validate(array $log)
    {
        $errors = [];
        if (empty($log['date'])) {
            $errors['date'] = "date can't be blank";
        } elseif (mb_strlen($log['date']) > 30) {
            $errors['date'] = "date can't to be more than 30 chars";
        }

        if (empty($log['descr'])) {
            $errors['descr'] = "Description can't be blank";
        }

        $select = ['Success', 'Wait', 'Error', 'Debug'];
        if (!in_array($log['level'], $select, true)) {
            $errors['level'] = "Please! Don't do this.";
        }

        if (!preg_match("/^[0-9]+(\,|\.)[0-9]+$/", $log['control'])) {
            $errors['control'] = "control must be float num";
        }

        return $errors;
    }
}
