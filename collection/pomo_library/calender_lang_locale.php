<?php

class Calender_Locale {

	public $weekday;
	public $weekday_initial;
	public $weekday_abbrev;
	public $start_of_week;
	public $month;
	public $month_genitive;
	public $month_abbrev;
	public $meridiem;
	public $text_direction = 'ltr';
	public $number_format;
	public function __construct() {
		$this->init();
		$this->register_globals();
	}
	public function init() {
		$this->weekday[0] =  __('Sunday');
		$this->weekday[1] =  __('Monday');
		$this->weekday[2] =  __('Tuesday');
		$this->weekday[3] =  __('Wednesday');
		$this->weekday[4] =  __('Thursday');
		$this->weekday[5] =  __('Friday');
		$this->weekday[6] =  __('Saturday');
		$this->weekday_initial[ __( 'Sunday' ) ]    =  __( 'S' );
		$this->weekday_initial[ __( 'Monday' ) ]    =  __( 'M' );
		$this->weekday_initial[ __( 'Tuesday' ) ]   =  __( 'T' );
		$this->weekday_initial[ __( 'Wednesday' ) ] =  __( 'W' );
		$this->weekday_initial[ __( 'Thursday' ) ]  = __( 'T' );
		$this->weekday_initial[ __( 'Friday' ) ]    = __( 'F' );
		$this->weekday_initial[ __( 'Saturday' ) ]  = __( 'S' );
		$this->weekday_abbrev[__('Sunday')]    =  __('Sun');
		$this->weekday_abbrev[__('Monday')]    =  __('Mon');
		$this->weekday_abbrev[__('Tuesday')]   =  __('Tue');
		$this->weekday_abbrev[__('Wednesday')] =  __('Wed');
		$this->weekday_abbrev[__('Thursday')]  =  __('Thu');
		$this->weekday_abbrev[__('Friday')]    =  __('Fri');
		$this->weekday_abbrev[__('Saturday')]  =  __('Sat');
		$this->month['01'] =  __( 'January' );
		$this->month['02'] =  __( 'February' );
		$this->month['03'] =  __( 'March' );
		$this->month['04'] =  __( 'April' );
		$this->month['05'] =  __( 'May' );
		$this->month['06'] =  __( 'June' );
		$this->month['07'] =  __( 'July' );
		$this->month['08'] =  __( 'August' );
		$this->month['09'] =  __( 'September' );
		$this->month['10'] =  __( 'October' );
		$this->month['11'] =  __( 'November' );
		$this->month['12'] =  __( 'December' );
		$this->month_genitive['01'] =  __( 'January' );
		$this->month_genitive['02'] =  __( 'February' );
		$this->month_genitive['03'] =  __( 'March' );
		$this->month_genitive['04'] =  __( 'April' );
		$this->month_genitive['05'] =  __( 'May' );
		$this->month_genitive['06'] =  __( 'June' );
		$this->month_genitive['07'] =  __( 'July' );
		$this->month_genitive['08'] =  __( 'August' );
		$this->month_genitive['09'] =  __( 'September' );
		$this->month_genitive['10'] =  __( 'October' );
		$this->month_genitive['11'] =  __( 'November' );
		$this->month_genitive['12'] =  __( 'December' );
		$this->month_abbrev[ __( 'February' ) ]  =  __( 'Feb' );
		$this->month_abbrev[ __( 'March' ) ]     =  __( 'Mar' );
		$this->month_abbrev[ __( 'April' ) ]     =  __( 'Apr' );
		$this->month_abbrev[ __( 'May' ) ]       =  __( 'May' );
		$this->month_abbrev[ __( 'June' ) ]      =  __( 'Jun' );
		$this->month_abbrev[ __( 'July' ) ]      =  __( 'Jul' );
		$this->month_abbrev[ __( 'August' ) ]    =  __( 'Aug' );
		$this->month_abbrev[ __( 'September' ) ] =  __( 'Sep' );
		$this->month_abbrev[ __( 'October' ) ]   =  __( 'Oct' );
		$this->month_abbrev[ __( 'November' ) ]  =  __( 'Nov' );
		$this->month_abbrev[ __( 'December' ) ]  =  __( 'Dec' );
		$this->meridiem['am'] = __('am');
		$this->meridiem['pm'] = __('pm');
		$this->meridiem['AM'] = __('AM');
		$this->meridiem['PM'] = __('PM');
		$thousands_sep = __( 'number_format_thousands_sep' );
		if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
			$thousands_sep = str_replace( ' ', '&nbsp;', $thousands_sep );
		} else {
			$thousands_sep = str_replace( array( '&nbsp;', '&#160;' ), ' ', $thousands_sep );
		}

		$this->number_format['thousands_sep'] = ( 'number_format_thousands_sep' === $thousands_sep ) ? ',' : $thousands_sep;
		$decimal_point = __( 'number_format_decimal_point' );
		$this->number_format['decimal_point'] = ( 'number_format_decimal_point' === $decimal_point ) ? '.' : $decimal_point;
		if ( isset( $GLOBALS['text_direction'] ) )
			$this->text_direction = $GLOBALS['text_direction'];
		elseif ( 'rtl' == _x( 'ltr', 'text direction' ) )
			$this->text_direction = 'rtl';
		if ( 'rtl' === $this->text_direction && strpos( get_bloginfo( 'version' ), '-src' ) ) {
			$this->text_direction = 'ltr';
			add_action( 'all_admin_notices', array( $this, 'rtl_src_admin_notice' ) );
		}
	}
	public function rtl_src_admin_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'The %s directory of the develop repository must be used for RTL.' ), '<code>build</code>' ) . '</p></div>';
	}
	public function get_weekday($weekday_number) {
		return $this->weekday[$weekday_number];
	}
	public function get_weekday_initial($weekday_name) {
		return $this->weekday_initial[$weekday_name];
	}
	public function get_weekday_abbrev($weekday_name) {
		return $this->weekday_abbrev[$weekday_name];
	}
	public function get_month($month_number) {
		return $this->month[zeroise($month_number, 2)];
	}
	public function get_month_abbrev($month_name) {
		return $this->month_abbrev[$month_name];
	}
	public function get_meridiem($meridiem) {
		return $this->meridiem[$meridiem];
	}
	public function register_globals() {
		$GLOBALS['weekday']         = $this->weekday;
		$GLOBALS['weekday_initial'] = $this->weekday_initial;
		$GLOBALS['weekday_abbrev']  = $this->weekday_abbrev;
		$GLOBALS['month']           = $this->month;
		$GLOBALS['month_abbrev']    = $this->month_abbrev;
	}
	public function is_rtl() {
		return 'rtl' == $this->text_direction;
	}
	public function _strings_for_pot() {
		__( 'F j, Y' );
		__( 'g:i a' );
		__( 'F j, Y g:i a' );
	}
}
