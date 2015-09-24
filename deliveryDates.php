<?php
date_default_timezone_set('America/Toronto');
require('psysh');
// eval(\Psy\sh())

function timestamp_diff($timestamp_1, $timestamp_2)
{
 return abs($timestamp_1 - $timestamp_2)/60/60/24;
}

class deliveryDates {
	/** Properties **/
	private $available_dates = array();
    private $blackout_dates = array();

	public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct($blackout_start = null, $blackout_end = null)
    {
        $blackout_dates = array(
            "start" => $blackout_start,
            "end" => $blackout_end
            )
    }


    public function update_blackout($blackout_start, $blackout_end)
    {
        $blackout_dates['start'] = $blackout_start;
        $blackout_dates['end'] = $blackout_end;
    }

    public function is_in_blackout($delivery_date_timestamp)
    {
        if ($delivery_date_timestamp > $blackout_dates['start']) && ($delivery_date_timestamp < $blackout_dates['end']) {
            return true;
        } else {
            return false;
        }

    }

    public function get_available_dates($number_of_dates = 2) /** We assume we want the next 2 dates, but we could ask for more **/
    {
        $current_date = strtotime("now");
        $available_dates = array();
        $comparison_date = $current_date;
        // eval(\Psy\sh());
        while (count($available_dates) < $number_of_dates) {
            $comparison_date = strtotime("+1 day", $comparison_date);
            // Check if date is a Monday, if it hasn't missed cutoff, and if it's not in the blackout period.
            if (date("N", $comparison_date) == 1 && ($this->has_missed_cutoff($comparison_date) == false)) {
                array_push($available_dates, 
                    array(
                        "timestamp" => $comparison_date,
                        "cutoff" => strtotime('-4 days', $comparison_date)
                        )
                    );
            }
        }
        return $available_dates;
    }


    public function has_missed_cutoff($delivery_date_timestamp)
    {
        $current_date = strtotime('now');
        return timestamp_diff($current_date, $delivery_date_timestamp) < 4;
    }
}


?>
<html>
<body>
    <p>
     <strong>Next 2 available delivery dates:</strong><br />
     <ul>
      <?php foreach(deliveryDates::getInstance()->get_available_dates() as $date): ?>

        <li>Delivery on <?=date('Y-m-d',$date['timestamp'])?> with a cut-off time of <?=date('Y-m-d',$date['cutoff'])?></li>      
    <?php endforeach ?>
</ul>
</p>
<p>
	<strong>Have I missed the cutoff for delivery on 2015-09-28 ?</strong><br />
	<?=(deliveryDates::getInstance()->has_missed_cutoff(strtotime('2015-09-28')) ? 'Yes' : 'No')?>
</p>
</body>
</html>