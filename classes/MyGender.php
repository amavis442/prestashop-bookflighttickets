<?php
class MyGender extends ObjectModel
{
    public $id_gender;
    public $id_lang;
    public $name;

    public static $definition = array(
        'table' => 'gender_lang',
        'primary' => 'id_gender',
        'multilang' => true,

        'fields' => array(
            'id_gender' => array(
                'type' => self::TYPE_INT,
            ),
            'id_lang' => array(
                'type' => self::INT,
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            )
        ),
    );

    public static function getGender($id_gender) {
        $sql = 'SELECT name FROM '._DB_PREFIX_.'gender_lang WHERE id_gender = '.$id_gender;
        return Db::getInstance()->getValue($sql);
    }

}