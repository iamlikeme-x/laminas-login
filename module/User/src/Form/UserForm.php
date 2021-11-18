<?php

namespace User\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Form;
use Laminas\Validator\StringLength;
use User\Model\User;

class UserForm extends Form
{
    public const FIELDS = [
        'id',
        'username',
        'passwd',
        [
            'name' => 'submit',
            'type' => 'submit',
            'options' => [
                'value' => 'Go',
                'id' => 'submitbutton',
            ],
        ],
    ];

    public function __construct($name = null)
    {
        parent::__construct($name ?? "user");

        foreach (self::FIELDS as $field) {
            if (is_string($field)) {
                $field = User::getField($field);
            }
            $this->add($field);
        }
    }
}