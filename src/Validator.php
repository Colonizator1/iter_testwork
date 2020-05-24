<?php

namespace Iter;

class Validator
{
    public function validate(array $log)
    {
        $errors = [];
        
        if (empty($log['date'])) {
            $errors['date'] = "date can't be blank";
        } elseif (!is_numeric(strtotime($log['date']))) {
            $errors['date'] = "Incorrect date";
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
