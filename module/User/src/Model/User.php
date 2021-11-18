<?php

namespace User\Model;

use Laminas\InputFilter\InputFilterAwareInterface;

use DomainException;
use Laminas\Crypt\Password\BcryptSha;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Loader\Exception\RuntimeException;

class User implements InputFilterAwareInterface
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $passwd;

    /**
     * @var InputFilterInterface
     */
    protected $inputFilter;

    /**
     * @var BcryptSha
     */
    protected $crypt;

    /**
     * @var array
     */
    protected static $fields;

    public function __construct()
    {
        $this->crypt = new BcryptSha();
    }

    public function exchangeArray(array $data): void
    {
        $fieldsArr = $this::getFields();

        foreach ($fieldsArr as $fieldArr) {
            $field = $fieldArr['name'];

            if (!isset($data[$field])) {
                continue;
            }
            
            switch ($field) {
                case 'passwd':
                    $this->$field = $this->crypt->create($data[$field]);
                    break;
                default:
                    $this->$field =  $data[$field];
            }
        }
    }

    public function getArrayCopy(): array
    {
        $fieldsArr = $this::getFields();

        $data = [];

        foreach ($fieldsArr as $fieldArr) {
            $field = $fieldArr['name'];
            $data[$field] = $this->$field;
        }

        return $data;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            "%s does not allow injection of an alternative input filter",
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
        if (!isset($this->inputFilter)) {    
            $inputFilter = new InputFilter();
            $fields = $this::getFields();

            foreach ($fields as $field) {
                $filterArr = [
                    'name' => $field['name'],
                    'required' => $field['required'] ?? false,
                    'filters' => $field['filters'] ?? [],
                    'validators' => $field['validators'] ?? [],
                ];

                $inputFilter->add($filterArr);
            }

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;        
    }

    public static function getField(string $field): array
    {
        $fields = self::getFields();
        
        if (!isset($fields[$field])) {
            throw new RuntimeException("Could not find field '$field'");
        }

        return self::$fields[$field];
    }


    public static function getFields(): array
    {
        if (!isset(self::$fields)) {
            self::$fields = include 'fields.php';
        }

        return self::$fields;
    }
}