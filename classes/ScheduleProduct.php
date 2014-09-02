<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReservationProduct
 *
 * @author patrick
 */
class ScheduleProduct extends ObjectModel
{

    //put your code here
    public static $definition = array(
        'table' => 'bookflighttickets_schedule_product',
        'primary' => 'id_scheduleproduct',
        'multilang' => false,
        'fields' => array(
            'id_scheduleproduct' => array(
                'type' => self::TYPE_INT,
            ),
            'id_schedule' => array(
                'type' => self::TYPE_INT,
            ),
            'id_product' => array(
                'type' => self::TYPE_INT,
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE
            ),
        ),
    );

    /**
     * 
     * @param int $id_schedule
     * @return int|null
     */
    public static function findProductIdByScheduleId($id_schedule)
    {
        $sql = 'select id_product from ' . _ - DB_PREFIX_ . self::$definition['table'] . ' WHERE id_schedule = ' . (int) $id_schedule;
        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result['id_product'];
        } else {
            return null;
        }
    }

}
