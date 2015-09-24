<?php
date_default_timezone_set('America/Toronto');
class deliveryDates {

	/** Properties **/
	private $available_dates = array();

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
    protected function __construct()
    {

    }

    public function get_available_dates($number_of_dates = 2) /** We assume we want the next 2 dates, but we could ask for more **/
    {
        $current_date = strtotime('now');
        $available_dates = array();
        $comparison_date = $current_date;
        while (count($available_dates) <= $number_of_dates) {
            $comparison_date = strtotime('+1 day', $comparison_date);
            // if (date('N', $comparison_date) == 1 && (date_diff($current_date, $comparison_date) >= 4 )) {
            if (date('N', $comparison_date) == 1) {
                array_push($available_dates, $comparison_date);
            }
        }
        return $available_dates;


    }

    public function has_missed_cutoff($delivery_date_timestamp)
    {

    }
}


?>
<html>
<body>
<p>
	<strong>Next 2 available delivery dates:</strong><br />
	<ul>
		<?php foreach(deliveryDates::getInstance()->get_available_dates() as $date): ?>
			<li>Delivery on <?=date('Y-m-d',$date['timestamp'])?> with a cut-off time of <?=date('Y-m-d H:i:s',$date['cutoff'])?></li>		
		<?php endforeach ?>
	</ul>
</p>
<p>
	<strong>Have I missed the cutoff for delivery on 2015-09-28 ?</strong><br />
	<?=(deliveryDates::getInstance()->has_missed_cutoff(strtotime('2015-09-28')) ? 'Yes' : 'No')?>
</p>
</body>
</html>