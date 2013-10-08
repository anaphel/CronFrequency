<?php

/**
 * Try to make the cron frequency easy to read
 * 
 * @author Louis Hatier
 */
class CronFrequency
{
    const MINUTE       = 1;
    const HOUR         = 2;
    const DAY_OF_MONTH = 3;
    const MONTH        = 4;
    const DAY_OF_WEEK  = 5;
    
    private $_minute;
    private $_hour;
    private $_dayOfMonth;
    private $_month;
    private $_dayOfWeek;

    // possible unique value for each field
    private $_values = array(
        self::MINUTE       => array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59),
        self::HOUR         => array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23),
        self::DAY_OF_MONTH => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31),
        self::MONTH        => array('1','2','3','4','5','6','7','8','9','10','11','12','jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'),
        self::DAY_OF_WEEK  => array('0','1','2','3','4','5','6','7','sun','mon','tue','wed','thu','fri','sat')
    );

    // text for days and months
    private $_text = array(
        self::MONTH => array(
            '1'   => 'january',
            '2'   => 'february',
            '3'   => 'march',
            '4'   => 'april',
            '5'   => 'may',
            '6'   => 'june',
            '7'   => 'july',
            '8'   => 'august',
            '9'   => 'september',
            '10'  => 'october',
            '11'  => 'november',
            '12'  => 'december',
            'jan' => 'january',
            'feb' => 'february',
            'mar' => 'march',
            'apr' => 'april',
            'may' => 'may',
            'jun' => 'june',
            'jul' => 'july',
            'aug' => 'august',
            'sep' => 'september',
            'oct' => 'october',
            'nov' => 'november',
            'dec' => 'december'
        ),
        self::DAY_OF_WEEK => array(
            '0'   => 'sunday',
            '1'   => 'monday',
            '2'   => 'tuesday',
            '3'   => 'wednesday',
            '4'   => 'thursday',
            '5'   => 'friday',
            '6'   => 'saturday',
            '7'   => 'sunday',
            'sun' => 'sunday',
            'mon' => 'monday',
            'tue' => 'tuesday',
            'wed' => 'wednesday',
            'thu' => 'thursday',
            'fri' => 'friday',
            'sat' => 'saturday'
        )
    );

    /**
     * Feed the constructor with the cron frequency
     * 
     * @param string $cronFrequency
     * @return void
     */
    public function __construct($cronFrequency)
    {
        $data = explode(' ', $cronFrequency, 5);
        if (count($data) === 5) {
            list($minute, $hour, $dayOfMonth, $month, $dayOfWeek) = $data;
            $this->_minute = $minute;
            $this->_hour = $hour;
            $this->_dayOfMonth = $dayOfMonth;
            $this->_month = $month;
            $this->_dayOfWeek = $dayOfWeek;
        } else {
            throw new Exception('Cron frequency must countain 5 informations');
        }
    }

    public function toHuman()
    {
        $output = array();

        // if not every minute
        if (!$this->_every($this->_minute)) {
            // the minute has a unique value
            if (is_numeric($this->_minute) && in_array($this->_minute, $this->_values[self::MINUTE])) {
                // every hour
                if ($this->_every($this->_hour)) {
                    $output[] = 'at the ' . $this->_minute . $this->_getNumberSuffix($this->_minute) . ' minute of every hour';
                // the hour has a unique value
                } elseif (is_numeric($this->_hour) && in_array($this->_hour, $this->_values[self::HOUR])) {
                    // some specific stuff
                    if (($this->_minute == 0) && ($this->_hour == 0)) {
                        $output[] = 'at midnight';
                    } elseif (($this->_minute == 0) && ($this->_hour == 12)) {
                        $output[] = 'at midday';
                    } else {
                        $output[] = 'at ' . $this->_format($this->_hour) . ':' . $this->_format($this->_minute);
                    }
                // the hour has a special value
                } else {
                    $output[] = 'at the ' . $this->_minute . $this->_getNumberSuffix($this->_minute) . ' minute of ' . $this->_describeMe($this->_hour, self::HOUR) . ' hour';
                }
            // the minute has a special value
            } else {
                $tmpMinute = $this->_describeMe($this->_minute, self::MINUTE) . ' minute ';
            }
        // every minute
        } else {
            $tmpMinute = 'every minute ';
        }

        // add hour to * or special value of minute
        if (isset($tmpMinute)) {
            // every hour
            if ($this->_every($this->_hour)) {
                $tmpMinute .= 'of every hour';
            // the hour has a unique value
            } elseif (is_numeric($this->_hour) && in_array($this->_hour, $this->_values[self::HOUR])) {
                $tmpMinute .= 'at ' . $this->_hour;
            // the hour has a special value
            } else {
                $tmpMinute .= 'of ' . $this->_describeMe($this->_hour, self::HOUR) . ' hour';
            }
            $output[] = $tmpMinute;
        }

        // if not every day of the month
        if (!$this->_every($this->_dayOfMonth)) {
            // the day of month has a unique value
            if (is_numeric($this->_dayOfMonth) && in_array($this->_dayOfMonth, $this->_values[self::DAY_OF_MONTH])) {
                $output[] = 'the ' . $this->_dayOfMonth . $this->_getNumberSuffix($this->_minute) . ' day of the month';
            // the day of month has a special value
            } else {
                $output[] = $this->_describeMe($this->_dayOfMonth, self::DAY_OF_MONTH) . ' day of the month';
            }
        } else {
            // only if day of week is not specific
            if ($this->_every($this->_dayOfWeek)) {
                $output[] = 'every day';
            }
        }

        // if not every month
        if (!$this->_every($this->_month)) {
            // the month has a unique value
            if (is_string($this->_month) && in_array($this->_month, $this->_values[self::MONTH])) {
                $output[] = 'of ' . $this->_text[self::MONTH][$this->_month];
            // the month has a special value
            } else {
                $output[] = $this->_describeMe($this->_month, self::MONTH);
            }
        }

        // if not every day of the week
        if (!$this->_every($this->_dayOfWeek)) {
            // the day of week has a unique value
            if (is_string($this->_dayOfWeek) && in_array($this->_dayOfWeek, $this->_values[self::DAY_OF_WEEK])) {
                $output[] = 'of every ' . $this->_text[self::DAY_OF_WEEK][$this->_dayOfWeek];
            // the day of week has a special value
            } else {
                $output[] = $this->_describeMe($this->_dayOfWeek, self::DAY_OF_WEEK);
            }
        }

        return implode(' ', $output);
    }

    /**
     * Try to describe the field that has probably stuff like "," and "-" or even "/" !
     * 
     * @param string $field
     * @param integer $type
     * @return string
     */
    private function _describeMe($field, $type)
    {
        $parts = explode(',', $field);
        $period = array();
        $partIn = 0;

        foreach ($parts as $part) {
            if (strpos($part, '-') !== false) {
                if (is_string($part)) {
                    $part = strtolower($part);
                }
                switch ($type) {
                    case self::MONTH:
                        $months = explode('-', $part);
                        foreach ($months as $key => $month) {
                            if (in_array($month, $this->_values[self::MONTH])) {
                                $months[$key] = $this->_text[self::MONTH][$month];
                            }
                        }
                        $period[] = 'from ' . implode(' to ', $months);
                        break;
                    case self::DAY_OF_WEEK:
                        $daysOfWeek = explode('-', $part);
                        foreach ($daysOfWeek as $key => $dayOfWeek) {
                            if (in_array($dayOfWeek, $this->_values[self::DAY_OF_WEEK])) {
                                $daysOfWeek[$key] = $this->_text[self::DAY_OF_WEEK][$dayOfWeek];
                            }
                        }
                        $period[] = 'from ' . implode(' to ', $daysOfWeek);
                        break;
                    default:
                        $period[] = str_replace('-', ' to ', $part);
                }
            } elseif (strpos($part, '*/') !== false) {
                switch ($type) {
                    case self::MONTH:
                        $period[] = str_replace('*/', 'every ', $part) . ' month';
                        break;
                    case self::DAY_OF_WEEK:
                        $period[] = str_replace('*/', 'every ', $part) . ' days';
                        break;
                    default:
                        $period[] = str_replace('*/', 'every ', $part);
                }
            } else {
                if (is_string($part)) {
                    $part = strtolower($part);
                }
                switch ($type) {
                    case self::MONTH:
                        if (in_array($part, $this->_values[self::MONTH])) {
                            if ($partIn === 0) {
                                $str = 'in ';
                            } else {
                                $str = '';
                            }
                            $period[] = $str . $this->_text[self::MONTH][$part];
                            $partIn++;
                        }
                        break;
                    case self::DAY_OF_WEEK:
                        if (in_array($part, $this->_values[self::DAY_OF_WEEK])) {
                            if ($partIn === 0) {
                                $str = 'in ';
                            } else {
                                $str = '';
                            }
                            $period[] = $str . $this->_text[self::DAY_OF_WEEK][$part];
                            $partIn++;
                        }
                        break;
                    default:
                        $period[] = $part;
                }
            }
        }

        // some specific stuff
        $result = implode(', ', $period);
        if (($result === 'from saturday to sunday') || ($result === 'in saturday, sunday')) {
            $result = 'on weekend';
        }

        return $result;
    }

    /**
     * Return the field (minute or hour) formated
     * 
     * @param integer $field
     * @return string
     */
    private function _format($field)
    {
        return str_pad($field, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Return if the field is "*"
     * 
     * @param integer|string $field
     * @return bool
     */
    private function _every($field)
    {
        if ($field === '*') {
            return true;
        }
        return false;
    }

    /**
     * Return the number's suffix
     * 
     * @param integer $number
     * @return string
     */
    private function _getNumberSuffix($number)
    {
        switch ($number) {
            case 1:
                return 'st';
            case 2:
                return 'nd';
            case 3:
                return 'rd';
            default:
                return 'th';
        }
    }
}
