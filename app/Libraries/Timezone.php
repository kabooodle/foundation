<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries;

/**
 * Class Timezone
 * @package Kabooodle\Libraries
 */
class Timezone
{
    /**
     * @return array
     */
  public static function timezoneList()
  {
      $timezone = array(
          'Pacific/Honolulu' => '(GMT-10:00) Hawaiian/Aleutian Time',
          'America/Anchorage' => '(GMT-08:00) Alaska Time',
          'America/Los_Angeles' => '(GMT-07:00) Pacific Time (US)',
          'America/Phoenix' => '(GMT-07:00) Mountain Time (Arizona)',
          'America/Denver' => '(GMT-06:00) Mountain Time (US)',
          'America/Chicago' => '(GMT-05:00) Central Time (US)',
          'America/New_York' => '(GMT-04:00) Eastern Time (US)',
          'America/Indiana/Knox' => '(GMT-05:00) Central Time (Indiana)',
          'America/Indiana/Indianapolis' => '(GMT-04:00) Eastern Time (Indiana)',
//          '--SEPARATOR1--' => '-------------',
//          'America/Indiana/Knox' => '(GMT-05:00) Central Time (Indiana)',
//          'America/Indiana/Indianapolis' => '(GMT-04:00) Eastern Time (Indiana)',
//          '--SEPARATOR2--' => '-------------',
//          'America/Regina' => '(GMT-06:00) Central Time (Saskatchewan)',
//          'America/Monterrey' => '(GMT-05:00) Central Time (Mexico City, Monterey)',
//          'America/Lima' => '(GMT-05:00) UTC/GMT -5 hours',
//          'America/Manaus' => '(GMT-04:00) Atlantic Time',
//          'America/Puerto_Rico' => '(GMT-04:00) Atlantic Time (Puerto Rico)',
//          'America/Thule' => '(GMT-03:00) Western Greenland Time',
//          'America/Sao_Paulo' => '(GMT-03:00) Eastern Brazil',
//          'America/St_Johns' => '(GMT-02:30) Newfoundland Time',
//          'America/Godthab' => '(GMT-02:00) Central Greenland Time',
//          'Etc/GMT+2' => '(GMT-02:00) GMT-2:00',
//          'America/Scoresbysund' => '(GMT+00:00) Eastern Greenland Time',
//          'Atlantic/Reykjavik' => '(GMT+00:00) Western European Time (Iceland)',
//          'UTC' => '(GMT+00:00) UTC',
//          'Europe/London' => '(GMT+01:00) British Time (London)',
//          'Etc/GMT-1' => '(GMT+01:00) GMT+1:00',
//          'Europe/Lisbon' => '(GMT+01:00) Western European Time (Lisbon)',
//          'Europe/Paris' => '(GMT+02:00) Western European Time',
//          'Europe/Berlin' => '(GMT+02:00) Central European Time',
//          'Europe/Bucharest' => '(GMT+03:00) Eastern European Time',
//          'Africa/Kampala' => '(GMT+03:00) Eastern Africa Time',
//          'Etc/GMT-3' => '(GMT+03:00) Moscow',
//          'Asia/Tehran' => '(GMT+03:30) Iran Standard Time',
//          'Asia/Dubai' => '(GMT+04:00) UAE (Dubai)',
//          'Asia/Karachi' => '(GMT+05:00) Pakistan Standard Time (Karachi)',
//          'Asia/Calcutta' => '(GMT+05:30) India',
//          'Asia/Dhaka' => '(GMT+06:00) Bangladesh Standard Time',
//          'Asia/Jakarta' => '(GMT+07:00) Western Indonesian Time (Jakarta)',
//          'Asia/Bangkok' => '(GMT+07:00) Thailand (Bangkok)',
//          'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
//          'Asia/Singapore' => '(GMT+08:00) Singapore',
//          'Australia/West' => '(GMT+08:00) Australian Western Time',
//          'Asia/Tokyo' => '(GMT+09:00) Tokyo',
//          'Australia/North' => '(GMT+09:30) Australian Central Time (Northern Territory)',
//          'Australia/Adelaide' => '(GMT+10:30) Australian Central Time (Adelaide)',
//          'Australia/Queensland' => '(GMT+10:00) Australian Eastern Time (Queensland)',
//          'Australia/Sydney' => '(GMT+11:00) Australian Eastern Time (Sydney)',
//          'Pacific/Noumea' => '(GMT+11:00) Noumea, New Caledonia',
//          'Pacific/Norfolk' => '(GMT+11:00) Norfolk Island (Austl.)',
//          'Pacific/Tarawa' => '(GMT+12:00) Tarawa',
//          'Pacific/Auckland' => '(GMT+13:00) New Zealand Time',
      );


      return $timezone;
  }
}
