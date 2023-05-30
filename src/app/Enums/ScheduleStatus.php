<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 15/11/20
 * Time: 12:46 PM
 */

namespace App\Enums;


class ScheduleStatus
{
    public const ACTIVE = '1';
    public const RESCHEDULE = '2';
    public const POSTPONE = '3';
    public const CLOSED = '4';
}
