<?php
/*
Plugin Name: Shop Calendar
Plugin URI: http://raizzenet.com/
Description: 店舗向けのカレンダーで、店休日をウェジットに表示するプラグインです。
Version: 1.7.1
Author: Kazunari Matsumoto
Author URI: http://raizzenet.com/
*/
const MY_VERSION = '1.7.1';

if (class_exists('ShopCalendar')) {
	$shop_calendar = new ShopCalendar();
}

class ShopCalendar {

	private $options;
	private $week_day = array( "日曜&nbsp;&nbsp;", "月曜&nbsp;&nbsp;", "火曜&nbsp;&nbsp;", "水曜&nbsp;&nbsp;", "木曜&nbsp;&nbsp;", "金曜&nbsp;&nbsp;", "土曜&nbsp;&nbsp;", "なし" );
	private $chk_data = array( "0", "1", "2", "3", "4", "5", "6", "7");

	public function __construct() {
		register_activation_hook(__FILE__, array($this, 'activation'));
		register_uninstall_hook(__FILE__, 'ShopCalendar::uninstall');
		add_action('wp_enqueue_scripts', array($this, 'shop_calendar_scripts'));
		add_action('admin_menu', array($this, 'add_plugin_page'));
		add_action('admin_init', array($this, 'page_init'));
		add_shortcode('show_shop_calendar', array($this, 'show_calendar'));
		add_action('widgets_init', create_function('', 'register_widget("shopcalendarwidget");'));
		$this->check_my_options();
	}

	public function check_my_options() {
		$this->options = get_option('shop_calendar');
		if ($this->options == false) {
			$setting = array(
				'version' => MY_VERSION,
				'holiday' => array(),
				'holiday1st' => 0,
				'holiday2nd' => 0,
				'holiday3rd' => 0,
				'holiday4th' => 0,
				'holiday5th' => 0,
				'long_holiday1' => array(
					'year' => 0,
					'month' => 0,
					'start_day' => 0,
					'end_day' => 0
				),
				'long_holiday2' => array(
					'year' => 0,
					'month' => 0,
					'start_day' => 0,
					'end_day' => 0
				),
				'long_holiday3' => array(
					'year' => 0,
					'month' => 0,
					'start_day' => 0,
					'end_day' => 0
				),
				'long_holiday4' => array(
					'year' => 0,
					'month' => 0,
					'start_day' => 0,
					'end_day' => 0
				),
				'no_holiday' => array(
					'year' => 0,
					'month' => 0,
					'day' => 0
				),
				'no_holiday1' => array(
					'year' => 0,
					'month' => 0,
					'day' => 0
				),
				'no_holiday2' => array(
					'year' => 0,
					'month' => 0,
					'day' => 0
				),
				'no_holiday3' => array(
					'year' => 0,
					'month' => 0,
					'day' => 0
				)
			);
			update_option("shop_calendar", $setting);

		// バージョン1.1のオプション初期化
		} else if (count($this->options) == 11 || count($this->options) == 10) {
			$setting = array(
				'version' => MY_VERSION,
				'holiday' => $this->options['holiday'],
				'holiday1st' => $this->options['holiday1st'],
				'holiday2nd' => $this->options['holiday2nd'],
				'holiday3rd' => $this->options['holiday3rd'],
				'holiday4th' => $this->options['holiday4th'],
				'holiday5th' => $this->options['holiday5th'],
				'long_holiday1' => array(
					'year' => $this->options['long_holiday1']['year'],
					'month' => $this->options['long_holiday1']['month'],
					'start_day' => $this->options['long_holiday1']['start_day'],
					'end_day' => $this->options['long_holiday1']['end_day']
				),
				'long_holiday2' => array(
					'year' => $this->options['long_holiday2']['year'],
					'month' => $this->options['long_holiday2']['month'],
					'start_day' => $this->options['long_holiday2']['start_day'],
					'end_day' => $this->options['long_holiday2']['end_day']
				),
				'long_holiday3' => array(
					'year' => $this->options['long_holiday3']['year'],
					'month' => $this->options['long_holiday3']['month'],
					'start_day' => $this->options['long_holiday3']['start_day'],
					'end_day' => $this->options['long_holiday3']['end_day']
				),
				'long_holiday4' => array(
					'year' => 0,
					'month' => 0,
					'start_day' => 0,
					'end_day' => 0
				),
				'no_holiday' => array(
					'year' => "0",
					'month' => $this->options['no_holiday']['month'],
					'day' => $this->options['no_holiday']['day']
				),
				'no_holiday1' => array(
					'year' => "0",
					'month' => "0",
					'day' => "0"
				),
				'no_holiday2' => array(
					'year' => "0",
					'month' => "0",
					'day' => "0"
				),
				'no_holiday3' => array(
					'year' => "0",
					'month' => "0",
					'day' => "0"
				),
			);
			update_option("shop_calendar", $setting);

		// バージョン1.5のオプション初期化
		} else if (count($this->options) == 14) {
			$setting = array(
				'version' => MY_VERSION,
				'holiday' => $this->options['holiday'],
				'holiday1st' => $this->options['holiday1st'],
				'holiday2nd' => $this->options['holiday2nd'],
				'holiday3rd' => $this->options['holiday3rd'],
				'holiday4th' => $this->options['holiday4th'],
				'holiday5th' => $this->options['holiday5th'],
				'long_holiday1' => array(
					'year' => $this->options['long_holiday1']['year'],
					'month' => $this->options['long_holiday1']['month'],
					'start_day' => $this->options['long_holiday1']['start_day'],
					'end_day' => $this->options['long_holiday1']['end_day']
				),
				'long_holiday2' => array(
					'year' => $this->options['long_holiday2']['year'],
					'month' => $this->options['long_holiday2']['month'],
					'start_day' => $this->options['long_holiday2']['start_day'],
					'end_day' => $this->options['long_holiday2']['end_day']
				),
				'long_holiday3' => array(
					'year' => $this->options['long_holiday3']['year'],
					'month' => $this->options['long_holiday3']['month'],
					'start_day' => $this->options['long_holiday3']['start_day'],
					'end_day' => $this->options['long_holiday3']['end_day']
				),
				'long_holiday4' => array(
					'year' => 0,
					'month' => 0,
					'start_day' => 0,
					'end_day' => 0
				),
				'no_holiday' => array(
					'year' => "0",
					'month' => $this->options['no_holiday']['month'],
					'day' => $this->options['no_holiday']['day']
				),
				'no_holiday1' => array(
					'year' => "0",
					'month' => "0",
					'day' => "0"
				),
				'no_holiday2' => array(
					'year' => "0",
					'month' => "0",
					'day' => "0"
				),
				'no_holiday3' => array(
					'year' => "0",
					'month' => "0",
					'day' => "0"
				),
			);
			update_option("shop_calendar", $setting);

//		} else if (count($this->options) == 15) {
			// 次のバージョンでオプションが増えた時に記述する
		}
	}
	public function activation() {
		$this->check_my_options();
	}
	public static function uninstall() {
		delete_option('shop_calendar');
	}

	public function shop_calendar_scripts() {
		if (!is_admin()) {
			wp_enqueue_style('shop_calendar', plugin_dir_url( __FILE__ ) . '/shop-calendar.css', array());
		}
	}
	public function add_plugin_page() {
		$page_title = '定休日設定';
		$menu_slug = 'shop_calendar';
		$capability = 'manage_options';
		add_options_page($page_title, 'Shop Calendar設定', $capability, $menu_slug, array($this, 'set_param'));
	}

	public function page_init() {
		register_setting('shop_calendar_group', 'shop_calendar', array($this, 'sanitize'));
		add_settings_section('shop_calendar_section_id', '', '', 'shop_calendar');

		add_settings_field('holiday', '定休日', array($this, 'holiday_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('holiday1st', '定休日(第1曜日)', array($this, 'holiday1st_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('holiday2nd', '定休日(第2曜日)', array($this, 'holiday2nd_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('holiday3rd', '定休日(第3曜日)', array($this, 'holiday3rd_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('holiday4th', '定休日(第4曜日)', array($this, 'holiday4th_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('holiday5th', '定休日(第5曜日)', array($this, 'holiday5th_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('long_holiday1', '長期店休日1', array($this, 'long_holiday1_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('long_holiday2', '長期店休日2', array($this, 'long_holiday2_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('long_holiday3', '長期店休日3', array($this, 'long_holiday3_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('long_holiday4', '長期店休日4', array($this, 'long_holiday4_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('no_holiday', '臨時営業日1', array($this, 'no_holiday_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('no_holiday1', '臨時営業日2', array($this, 'no_holiday1_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('no_holiday2', '臨時営業日3', array($this, 'no_holiday2_callback'), 'shop_calendar', 'shop_calendar_section_id');
		add_settings_field('no_holiday3', '臨時営業日4', array($this, 'no_holiday3_callback'), 'shop_calendar', 'shop_calendar_section_id');
	}

	public function set_param() {
		echo '<div class="wrap">';
			echo '<h2>カレンダー設定</h2>';
			$this->options = get_option('shop_calendar');
//			var_dump($this->options); echo '<br/>';
			echo '<form method="post" action="options.php">';
				settings_fields('shop_calendar_group');
				do_settings_sections('shop_calendar');
				submit_button();
			echo '</form>';
		echo '</div>';
	}

	public function holiday_callback() {

		$this->options = get_option('shop_calendar');
		$selected = !empty($this->options['holiday']) ? $this->options['holiday'] : '';
		for ($i = 0; $i < 7; $i++) {
			$checked = false;
			if ($selected) {
				if (in_array($this->chk_data[$i], $selected)) {
					$checked = true;
				}
			}
			echo '<input type="checkbox" id="holiday' . $i . '" name="shop_calendar[holiday][]" value="'. $i . '"' . checked($checked, true, false) . '/>';
			echo '<label for=holiday'. $i . '>' . $this->week_day[$i] . '</label>';
		}
		echo "\n";
	}

	public function holiday1st_callback() {
		$this->options = get_option('shop_calendar');
		if ($this->options['holiday1st'] == "") {
			$selected = "7";
		} else {
			$selected = $this->options['holiday1st'];
		}

		for ($i = 0; $i < 8; $i++) {
			$checked = false;
			if ($selected != '') {
				if ($this->chk_data[$i] == $selected) {
					$checked = true;
				}
			}
			echo '<input type="radio" id="holiday1st' . $i .'" name="shop_calendar[holiday1st]" value="'. $i . '"' . checked($checked, true, false) . '/>';
			echo '<label for=holiday1st'. $i . '>' . $this->week_day[$i] . '</label>';
		}
		echo "\n";
	}

	public function holiday2nd_callback() {

		$this->options = get_option('shop_calendar');
		if ($this->options['holiday2nd'] == "") {
			$selected = "7";
		} else {
			$selected = $this->options['holiday2nd'];
		}

		for ($i = 0; $i < 8; $i++) {
			$checked = false;
			if ($selected != '') {
				if ($this->chk_data[$i] == $selected) {
					$checked = true;
				}
			}
			echo '<input type="radio" id="holiday2nd' . $i .'" name="shop_calendar[holiday2nd]" value="'. $i . '"' . checked($checked, true, false) . '/>';
			echo '<label for=holiday2nd'. $i . '>' . $this->week_day[$i] . '</label>';
		}
		echo "\n";
	}
	public function holiday3rd_callback() {

		$this->options = get_option('shop_calendar');
		if ($this->options['holiday3rd'] == "") {
			$selected = "7";
		} else {
			$selected = $this->options['holiday3rd'];
		}

		for ($i = 0; $i < 8; $i++) {
			$checked = false;
			if ($selected != '') {
				if ($this->chk_data[$i] == $selected) {
					$checked = true;
				}
			}
			echo '<input type="radio" id="holiday3rd' . $i .'" name="shop_calendar[holiday3rd]" value="'. $i . '"' . checked($checked, true, false) . '/>';
			echo '<label for=holiday3rd'. $i . '>' . $this->week_day[$i] . '</label>';
		}
		echo "\n";
	}
	public function holiday4th_callback() {

		$this->options = get_option('shop_calendar');
		if ($this->options['holiday4th'] == "") {
			$selected = "7";
		} else {
			$selected = $this->options['holiday4th'];
		}

		for ($i = 0; $i < 8; $i++) {
			$checked = false;
			if ($selected != '') {
				if ($this->chk_data[$i] == $selected) {
					$checked = true;
				}
			}
			echo '<input type="radio" id="holiday4th' . $i .'" name="shop_calendar[holiday4th]" value="'. $i . '"' . checked($checked, true, false) . '/>';
			echo '<label for=holiday4th'. $i . '>' . $this->week_day[$i] . '</label>';
		}
		echo "\n";
	}

	public function holiday5th_callback() {

		$this->options = get_option('shop_calendar');
		if ($this->options['holiday5th'] == "") {
			$selected = "7";
		} else {
			$selected = $this->options['holiday5th'];
		}

		for ($i = 0; $i < 8; $i++) {
			$checked = false;
			if ($selected != '') {
				if ($this->chk_data[$i] == $selected) {
					$checked = true;
				}
			}
			echo '<input type="radio" id="holiday5th' . $i .'" name="shop_calendar[holiday5th]" value="'. $i . '"' . checked($checked, true, false) . '/>';
			echo '<label for=holiday5th'. $i . '>' . $this->week_day[$i] . '</label>';
		}
		echo "\n";
	}

	public function long_holiday1_callback() {
		$this->options = get_option('shop_calendar');

		$start_year = $this->options['long_holiday1']['year'];
		$start_month = $this->options['long_holiday1']['month'];
		$start_day = $this->options['long_holiday1']['start_day'];
		$end_day = $this->options['long_holiday1']['end_day'];

		if ($start_year == "") {
			$start_year = 0;
		}
		if ($start_month == "") {
			$start_month = 0;
		}
		if ($start_day == "") {
			$start_day = 0;
		}
		if ($end_day == "") {
			$end_day = 0;
		}
		echo '<select name="shop_calendar[long_holiday1][year]">';
		echo '<option value="0">----</option>';
		$year = date_i18n('Y');
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		$year = date_i18n('Y', strtotime('+1 year'));
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		echo '</select>年&nbsp;';

		echo '<select name="shop_calendar[long_holiday1][month]">';
		for ($month = 0; $month <= 12; $month++) {
			$str = '<option value="' . $month . '"';
			if ($month == $start_month) {
				$str .= ' selected';
			}
			if ($month == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $month . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>月&nbsp;';

		echo '<select name="shop_calendar[long_holiday1][start_day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $start_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>日&nbsp;〜&nbsp;';

		echo '<select name="shop_calendar[long_holiday1][end_day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $end_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>日' . "\n";
	}

	public function long_holiday2_callback() {
		$this->options = get_option('shop_calendar');

		$start_year = $this->options['long_holiday2']['year'];
		$start_month = $this->options['long_holiday2']['month'];
		$start_day = $this->options['long_holiday2']['start_day'];
		$end_day = $this->options['long_holiday2']['end_day'];

		if ($start_year == "") {
			$start_year = 0;
		}
		if ($start_month == "") {
			$start_month = 0;
		}
		if ($start_day == "") {
			$start_day = 0;
		}
		if ($end_day == "") {
			$end_day = 0;
		}
		echo '<select name="shop_calendar[long_holiday2][year]">';
		$year = date_i18n('Y');
		echo '<option value="0">----</option>';
	if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		$year = date_i18n('Y', strtotime('+1 year'));
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		echo '</select>年&nbsp;';

		echo '<select name="shop_calendar[long_holiday2][month]">';
		for ($month = 0; $month <= 12; $month++) {
			$str = '<option value="' . $month . '"';
			if ($month == $start_month) {
				$str .= ' selected';
			}
			if ($month == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $month . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>月&nbsp;';

		echo '<select name="shop_calendar[long_holiday2][start_day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $start_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>日&nbsp;〜&nbsp;';

		echo '<select name="shop_calendar[long_holiday2][end_day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $end_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>日' . "\n";
	}

	public function long_holiday3_callback() {
		$this->options = get_option('shop_calendar');

		$start_year = $this->options['long_holiday3']['year'];
		$start_month = $this->options['long_holiday3']['month'];
		$start_day = $this->options['long_holiday3']['start_day'];
		$end_day = $this->options['long_holiday3']['end_day'];

		if ($start_year == "") {
			$start_year = 0;
		}
		if ($start_month == "") {
			$start_month = 0;
		}
		if ($start_day == "") {
			$start_day = 0;
		}
		if ($end_day == "") {
			$end_day = 0;
		}
		echo '<select name="shop_calendar[long_holiday3][year]">';
		echo '<option value="0">----</option>';
		$year = date_i18n('Y');
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		$year = date_i18n('Y', strtotime('+1 year'));
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		echo '</select>年&nbsp;';

		echo '<select name="shop_calendar[long_holiday3][month]">';
		for ($month = 0; $month <= 12; $month++) {
			$str = '<option value="' . $month . '"';
			if ($month == $start_month) {
				$str .= ' selected';
			}
			if ($month == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $month . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>月&nbsp;';

		echo '<select name="shop_calendar[long_holiday3][start_day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $start_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>日&nbsp;〜&nbsp;';

		echo '<select name="shop_calendar[long_holiday3][end_day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $end_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>日' . "\n";
	}

	public function long_holiday4_callback() {
		$this->options = get_option('shop_calendar');

		$start_year = $this->options['long_holiday4']['year'];
		$start_month = $this->options['long_holiday4']['month'];
		$start_day = $this->options['long_holiday4']['start_day'];
		$end_day = $this->options['long_holiday4']['end_day'];

		if ($start_year == "") {
			$start_year = 0;
		}
		if ($start_month == "") {
			$start_month = 0;
		}
		if ($start_day == "") {
			$start_day = 0;
		}
		if ($end_day == "") {
			$end_day = 0;
		}
		echo '<select name="shop_calendar[long_holiday4][year]">';
		echo '<option value="0">----</option>';
		$year = date_i18n('Y');
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		$year = date_i18n('Y', strtotime('+1 year'));
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		echo '</select>年&nbsp;';

		echo '<select name="shop_calendar[long_holiday4][month]">';
		for ($month = 0; $month <= 12; $month++) {
			$str = '<option value="' . $month . '"';
			if ($month == $start_month) {
				$str .= ' selected';
			}
			if ($month == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $month . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>月&nbsp;';

		echo '<select name="shop_calendar[long_holiday4][start_day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $start_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>日&nbsp;〜&nbsp;';

		echo '<select name="shop_calendar[long_holiday4][end_day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $end_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str . "\n";
		}
		echo '</select>日' . "\n";
	}

	public function no_holiday_callback() {
		$this->options = get_option('shop_calendar');
		$start_year = $this->options['no_holiday']['year'];
		$start_month = $this->options['no_holiday']['month'];
		$start_day = $this->options['no_holiday']['day'];

		if ($start_year == "") {
			$start_year = 0;
		}
		if ($start_month == "") {
			$start_month = 0;
		}
		if ($start_day == "") {
			$start_day = 0;
		}

//		echo date_i18n('Y') . '年&nbsp;';
		echo '<select name="shop_calendar[no_holiday][year]">';
		echo '<option value="0">----</option>';
		$year = date_i18n('Y');
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		$year = date_i18n('Y', strtotime('+1 year'));
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		echo '</select>年&nbsp;';

		echo '<select name="shop_calendar[no_holiday][month]">';
		for ($month = 0; $month <= 12; $month++) {
			$str = '<option value="' . $month . '"';
			if ($month == $start_month) {
				$str .= ' selected';
			}
			if ($month == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $month . '</option>';
			}
			echo $str;
		}
		echo '</select>月&nbsp;';

		echo '<select name="shop_calendar[no_holiday][day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $start_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str;
		}
		echo '</select>日' . "\n";
	}

	public function no_holiday1_callback() {
		$this->options = get_option('shop_calendar');
		$start_year = $this->options['no_holiday1']['year'];
		$start_month = $this->options['no_holiday1']['month'];
		$start_day = $this->options['no_holiday1']['day'];

		if ($start_year == "") {
			$start_year = 0;
		}
		if ($start_month == "") {
			$start_month = 0;
		}
		if ($start_day == "") {
			$start_day = 0;
		}

//		echo date_i18n('Y') . '年&nbsp;';
		echo '<select name="shop_calendar[no_holiday1][year]">';
		echo '<option value="0">----</option>';
		$year = date_i18n('Y');
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		$year = date_i18n('Y', strtotime('+1 year'));
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		echo '</select>年&nbsp;';

		echo '<select name="shop_calendar[no_holiday1][month]">';
		for ($month = 0; $month <= 12; $month++) {
			$str = '<option value="' . $month . '"';
			if ($month == $start_month) {
				$str .= ' selected';
			}
			if ($month == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $month . '</option>';
			}
			echo $str;
		}
		echo '</select>月&nbsp;';

		echo '<select name="shop_calendar[no_holiday1][day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $start_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str;
		}
		echo '</select>日' . "\n";
	}

	public function no_holiday2_callback() {
		$this->options = get_option('shop_calendar');
		$start_year = $this->options['no_holiday2']['year'];
		$start_month = $this->options['no_holiday2']['month'];
		$start_day = $this->options['no_holiday2']['day'];

		if ($start_year == "") {
			$start_year = 0;
		}
		if ($start_month == "") {
			$start_month = 0;
		}
		if ($start_day == "") {
			$start_day = 0;
		}

//		echo date_i18n('Y') . '年&nbsp;';
		echo '<select name="shop_calendar[no_holiday2][year]">';
		echo '<option value="0">----</option>';
		$year = date_i18n('Y');
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		$year = date_i18n('Y', strtotime('+1 year'));
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		echo '</select>年&nbsp;';

		echo '<select name="shop_calendar[no_holiday2][month]">';
		for ($month = 0; $month <= 12; $month++) {
			$str = '<option value="' . $month . '"';
			if ($month == $start_month) {
				$str .= ' selected';
			}
			if ($month == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $month . '</option>';
			}
			echo $str;
		}
		echo '</select>月&nbsp;';

		echo '<select name="shop_calendar[no_holiday2][day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $start_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str;
		}
		echo '</select>日' . "\n";
	}
	public function no_holiday3_callback() {
		$this->options = get_option('shop_calendar');
		$start_year = $this->options['no_holiday3']['year'];
		$start_month = $this->options['no_holiday3']['month'];
		$start_day = $this->options['no_holiday3']['day'];

		if ($start_year == "") {
			$start_year = 0;
		}
		if ($start_month == "") {
			$start_month = 0;
		}
		if ($start_day == "") {
			$start_day = 0;
		}

//		echo date_i18n('Y') . '年&nbsp;';
		echo '<select name="shop_calendar[no_holiday3][year]">';
		echo '<option value="0">----</option>';
		$year = date_i18n('Y');
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		$year = date_i18n('Y', strtotime('+1 year'));
		if ($start_year == $year) {
			$sel = 'selected';
		} else {
			$sel = ' ';
		}
		echo '<option value="' . $year . '"' . $sel . '>' . $year . '</option>';
		echo '</select>年&nbsp;';

		echo '<select name="shop_calendar[no_holiday3][month]">';
		for ($month = 0; $month <= 12; $month++) {
			$str = '<option value="' . $month . '"';
			if ($month == $start_month) {
				$str .= ' selected';
			}
			if ($month == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $month . '</option>';
			}
			echo $str;
		}
		echo '</select>月&nbsp;';

		echo '<select name="shop_calendar[no_holiday3][day]">';
		for ($day = 0; $day <= 31; $day++) {
			$str = '<option value="' . $day . '"';
			if ($day == $start_day) {
				$str .= ' selected';
			}
			if ($day == 0) {
				$str .= ' >----</option>';
			} else {
				$str .= ' >' . $day . '</option>';
			}
			echo $str;
		}
		echo '</select>日' . "\n";
	}

	public function sanitize($input) {

		$new_input = array();
		$this->options = get_option('shop_calendar');
		$new_input['version'] = MY_VERSION;
		/*
			店休日
		*/
		$holiday = $input['holiday'];
		if (!empty($holiday)) {
			$selected = array();
			foreach ($holiday as $h) {
				$selected[] = $h;
			}
			$new_input['holiday'] = isset($holiday) ? $selected : '';
		}
		/*
			店休日(曜日)
		*/
		$holiday = $input['holiday1st'];
		if (isset($holiday) && ($holiday >= "0" && $holiday <= "7")) {
			$new_input['holiday1st'] = $holiday;
		}
		$holiday = $input['holiday2nd'];
		if (isset($holiday) && ($holiday >= "0" && $holiday <= "7")) {
			$new_input['holiday2nd'] = $holiday;
		}
		$holiday = $input['holiday3rd'];
		if (isset($holiday) && ($holiday >= "0" && $holiday <= "7")) {
			$new_input['holiday3rd'] = $holiday;
		}
		$holiday = $input['holiday4th'];
		if (isset($holiday) && ($holiday >= "0" && $holiday <= "7")) {
			$new_input['holiday4th'] = $holiday;
		}
		$holiday = $input['holiday5th'];
		if (isset($holiday) && ($holiday >= "0" && $holiday <= "7")) {
			$new_input['holiday5th'] = $holiday;
		}

		/*
			長期店休日１
		*/
		$new_year = $input['long_holiday1']['year'];
		if (isset($new_year) && $new_year > 0) {
			$new_input['long_holiday1']['year'] = $new_year;

			$year = intval($new_year);
			$holiday = $input['long_holiday1']['month'];
			if (isset($holiday) && ($holiday >= "1" && $holiday <= "12")) {
				$new_input['long_holiday1']['month'] = $holiday;
			}
			else {
				$new_input['long_holiday1']['month'] = "0";
			}
			if ($holiday >= "1" && $holiday <= "12") {
				$month = intval($holiday);
				$last_day = strval(self::get_last_day($year, $month));

				$holiday = $input['long_holiday1']['start_day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['long_holiday1']['start_day'] = $holiday;
					} else {
						$new_input['long_holiday1']['start_day'] = $last_day;
					}
				}
				else {
					$new_input['long_holiday1']['start_day'] = "0";
				}
				$holiday = $input['long_holiday1']['end_day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['long_holiday1']['end_day'] = $holiday;
					} else {
						$new_input['long_holiday1']['end_day'] = $last_day;
					}
				}
				else {
					$new_input['long_holiday1']['end_day'] = "0";
				}
			}
			else {
				$new_input['long_holiday1']['start_day'] = "0";
				$new_input['long_holiday1']['end_day'] = "0";
			}
		}
		else {
			$new_input['long_holiday1']['year'] = "0";
			$new_input['long_holiday1']['month'] = "0";
			$new_input['long_holiday1']['start_day'] = "0";
			$new_input['long_holiday1']['end_day'] = "0";
		}
		/*
			長期店休日２
		*/
		$new_year = $input['long_holiday2']['year'];
		if (isset($new_year) && $new_year > 0) {
			$new_input['long_holiday2']['year'] = $new_year;

			$year = intval($new_year);
			$holiday = $input['long_holiday2']['month'];
			if (isset($holiday) && ($holiday >= "1" && $holiday <= "12")) {
				$new_input['long_holiday2']['month'] = $holiday;
			}
			else {
				$new_input['long_holiday2']['month'] = "0";
			}
			if ($holiday >= "1" && $holiday <= "12") {
				$month = intval($holiday);
				$last_day = strval(self::get_last_day($year, $month));

				$holiday = $input['long_holiday2']['start_day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['long_holiday2']['start_day'] = $holiday;
					} else {
						$new_input['long_holiday2']['start_day'] = $last_day;
					}
				}
				else {
					$new_input['long_holiday2']['start_day'] = "0";
				}
				$holiday = $input['long_holiday2']['end_day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['long_holiday2']['end_day'] = $holiday;
					} else {
						$new_input['long_holiday2']['end_day'] = $last_day;
					}
				}
				else {
					$new_input['long_holiday2']['end_day'] = "0";
				}
			}
			else {
				$new_input['long_holiday2']['start_day'] = "0";
				$new_input['long_holiday2']['end_day'] = "0";
			}
		} else {
			$new_input['long_holiday2']['year'] = "0";
			$new_input['long_holiday2']['month'] = "0";
			$new_input['long_holiday2']['start_day'] = "0";
			$new_input['long_holiday2']['end_day'] = "0";
		}
		/*
			長期店休日３
		*/
		$new_year = $input['long_holiday3']['year'];
		if (isset($new_year) && $new_year > 0) {
			$new_input['long_holiday3']['year'] = $new_year;

			$year = intval($new_year);
			$holiday = $input['long_holiday3']['month'];
			if (isset($holiday) && ($holiday >= "1" && $holiday <= "12")) {
				$new_input['long_holiday3']['month'] = $holiday;
			}
			else {
				$new_input['long_holiday3']['month'] = "0";
			}
			if ($holiday >= "1" && $holiday <= "12") {
				$month = intval($holiday);
				$last_day = strval(self::get_last_day($year, $month));

				$holiday = $input['long_holiday3']['start_day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['long_holiday3']['start_day'] = $holiday;
					} else {
						$new_input['long_holiday3']['start_day'] = $last_day;
					}
				}
				else {
					$new_input['long_holiday3']['start_day'] = "0";
				}
				$holiday = $input['long_holiday3']['end_day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['long_holiday3']['end_day'] = $holiday;
					} else {
						$new_input['long_holiday3']['end_day'] = $last_day;
					}
				}
				else {
					$new_input['long_holiday3']['end_day'] = "0";
				}
			}
			else {
				$new_input['long_holiday3']['start_day'] = "0";
				$new_input['long_holiday3']['end_day'] = "0";
			}

		} else {
			$new_input['long_holiday3']['year'] = "0";
			$new_input['long_holiday3']['month'] = "0";
			$new_input['long_holiday3']['start_day'] = "0";
			$new_input['long_holiday3']['end_day'] = "0";
		}
		/*
			長期店休日4
		*/
		$new_year = $input['long_holiday4']['year'];
		if (isset($new_year) && $new_year > 0) {
			$new_input['long_holiday4']['year'] = $new_year;

			$year = intval($new_year);
			$holiday = $input['long_holiday4']['month'];
			if (isset($holiday) && ($holiday >= "1" && $holiday <= "12")) {
				$new_input['long_holiday4']['month'] = $holiday;
			}
			else {
				$new_input['long_holiday4']['month'] = "0";
			}
			if ($holiday >= "1" && $holiday <= "12") {
				$month = intval($holiday);
				$last_day = strval(self::get_last_day($year, $month));

				$holiday = $input['long_holiday4']['start_day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['long_holiday4']['start_day'] = $holiday;
					} else {
						$new_input['long_holiday4']['start_day'] = $last_day;
					}
				}
				else {
					$new_input['long_holiday4']['start_day'] = "0";
				}
				$holiday = $input['long_holiday4']['end_day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['long_holiday4']['end_day'] = $holiday;
					} else {
						$new_input['long_holiday4']['end_day'] = $last_day;
					}
				}
				else {
					$new_input['long_holiday4']['end_day'] = "0";
				}
			}
			else {
				$new_input['long_holiday4']['start_day'] = "0";
				$new_input['long_holiday4']['end_day'] = "0";
			}

		} else {
			$new_input['long_holiday4']['year'] = "0";
			$new_input['long_holiday4']['month'] = "0";
			$new_input['long_holiday4']['start_day'] = "0";
			$new_input['long_holiday4']['end_day'] = "0";
		}

		/*
			臨時営業日1
		*/
		$new_year = $input['no_holiday']['year'];
		if (isset($new_year) && $new_year > 0) {
			$new_input['no_holiday']['year'] = $new_year;

			$year = intval($new_year);
			$holiday = $input['no_holiday']['month'];
			if (isset($holiday) && ($holiday >= "1" && $holiday <= "12")) {
				$new_input['no_holiday']['month'] = $holiday;
			}
			else {
				$new_input['no_holiday']['month'] = "0";
			}
			if ($holiday >= "1" && $holiday <= "12") {
				$month = intval($holiday);
				$last_day = strval(self::get_last_day($year, $month));

				$holiday = $input['no_holiday']['day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['no_holiday']['day'] = $holiday;
					} else {
						$new_input['no_holiday']['day'] = $last_day;
					}
				}
				else {
					$new_input['no_holiday']['day'] = "0";
				}
			}
			else {
				$new_input['no_holiday']['day'] = "0";
			}

		} else {
			$new_input['no_holiday']['year'] = "0";
			$new_input['no_holiday']['month'] = "0";
			$new_input['no_holiday']['day'] = "0";
		}

		/*
			臨時営業日2
		*/
		$new_year = $input['no_holiday1']['year'];
		if (isset($new_year) && $new_year > 0) {
			$new_input['no_holiday1']['year'] = $new_year;

			$year = intval($new_year);
			$holiday = $input['no_holiday1']['month'];
			if (isset($holiday) && ($holiday >= "1" && $holiday <= "12")) {
				$new_input['no_holiday1']['month'] = $holiday;
			}
			else {
				$new_input['no_holiday1']['month'] = "0";
			}
			if ($holiday >= "1" && $holiday <= "12") {
				$month = intval($holiday);
				$last_day = strval(self::get_last_day($year, $month));

				$holiday = $input['no_holiday1']['day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['no_holiday1']['day'] = $holiday;
					} else {
						$new_input['no_holiday1']['day'] = $last_day;
					}
				}
				else {
					$new_input['no_holiday1']['day'] = "0";
				}
			}
			else {
				$new_input['no_holiday1']['day'] = "0";
			}

		} else {
			$new_input['no_holiday1']['year'] = "0";
			$new_input['no_holiday1']['month'] = "0";
			$new_input['no_holiday1']['day'] = "0";
		}

		/*
			臨時営業日3
		*/
		$new_year = $input['no_holiday2']['year'];
		if (isset($new_year) && $new_year > 0) {
			$new_input['no_holiday2']['year'] = $new_year;

			$year = intval($new_year);
			$holiday = $input['no_holiday2']['month'];
			if (isset($holiday) && ($holiday >= "1" && $holiday <= "12")) {
				$new_input['no_holiday2']['month'] = $holiday;
			}
			else {
				$new_input['no_holiday2']['month'] = "0";
			}
			if ($holiday >= "1" && $holiday <= "12") {
				$month = intval($holiday);
				$last_day = strval(self::get_last_day($year, $month));

				$holiday = $input['no_holiday2']['day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['no_holiday2']['day'] = $holiday;
					} else {
						$new_input['no_holiday2']['day'] = $last_day;
					}
				}
				else {
					$new_input['no_holiday2']['day'] = "0";
				}
			}
			else {
				$new_input['no_holiday2']['day'] = "0";
			}

		} else {
			$new_input['no_holiday2']['year'] = "0";
			$new_input['no_holiday2']['month'] = "0";
			$new_input['no_holiday2']['day'] = "0";
		}

		/*
			臨時営業日4
		*/
		$new_year = $input['no_holiday3']['year'];
		if (isset($new_year) && $new_year > 0) {
			$new_input['no_holiday3']['year'] = $new_year;

			$year = intval($new_year);
			$holiday = $input['no_holiday3']['month'];
			if (isset($holiday) && ($holiday >= "1" && $holiday <= "12")) {
				$new_input['no_holiday3']['month'] = $holiday;
			}
			else {
				$new_input['no_holiday3']['month'] = "0";
			}
			if ($holiday >= "1" && $holiday <= "12") {
				$month = intval($holiday);
				$last_day = strval(self::get_last_day($year, $month));

				$holiday = $input['no_holiday3']['day'];
				if (isset($holiday) && ($holiday >= "1")) {
					if ($holiday <= $last_day) {
						$new_input['no_holiday3']['day'] = $holiday;
					} else {
						$new_input['no_holiday3']['day'] = $last_day;
					}
				}
				else {
					$new_input['no_holiday3']['day'] = "0";
				}
			}
			else {
				$new_input['no_holiday3']['day'] = "0";
			}

		} else {
			$new_input['no_holiday3']['year'] = "0";
			$new_input['no_holiday3']['month'] = "0";
			$new_input['no_holiday3']['day'] = "0";
		}

		return $new_input;
	}

	private function get_last_day($year, $month) {

		for ($day = 28; $day <= 31; $day++) {
			if (checkdate($month, $day, $year) == false) {
				$day = $day - 1;
				return $day;
			}
		}
		return $day;
	}

	public function show_calendar($atts) {
		extract(shortcode_atts(array(
			'year' => 0,
			'month' => 0,
			'next' => 0,
		), $atts));

		ob_start();

		if ($year == '' && $month == '' && $next == '') {
			$year = date_i18n('Y');
			$month = date_i18n('n');
		} else if ($next != '') {
			$val = intval($next);
			if ($val < 0) {
				$val = 0;
			}
			$str = '+' . $val . ' month';
			$month = date_i18n('m', strtotime(date_i18n('Y-m-01') . $str));
			$year = date_i18n('Y', strtotime(date_i18n('Y-m-01') . $str));
		} else {
			if ($year) {
				if ($year < 2000) {
					$year = 2000;
				}
			}
			if (!($month >= 1 && $month <= 12)) {
				$month = date_i18n('n');
			}
		}
		$day = date_i18n('j');
		self::draw($year, $month, $day);
		return ob_get_clean();
	}

	public function show() {
		$year = date_i18n('Y');
		$month = date_i18n('n');
		$day = date_i18n('j');
		self::draw($year, $month, $day);
	}
	private function draw($year, $month, $today) {

		$week_day = array( "日", "月", "火", "水", "木", "金", "土" );
		$w = date_i18n("w", mktime( 0, 0, 0, $month, 1, $year));
		$now_month = date_i18n('n');
		$this->options = get_option('shop_calendar');

		echo '<div class="shop-calendar">' . "\n";
		echo '<table class="shop-calendar-table">' . "\n";
		echo '<div class="caption">' . $year . '年' . $month . '月</div>' . "\n";
		echo "<tr>";
		for ($i = 0; $i < 7; $i++) {
			echo '<th>' . $week_day[$i] . '</th>';
		}
		echo '</tr>' . "\n" . '<tr>';
		$i = 0;
		while ($i != $w) {
			echo '<td>&nbsp;</td>';
			$i++;
		}
		echo "\n";

		$counter = 0;
		for ($day = 1; checkdate($month, $day, $year); $day++, $i++) {
			if ($i > 6) {
				$i = 0;
				echo '</tr>' . "\n" . '<tr>';
			}

			if ($now_month == $month && $day == $today) {
				$str = 'today';
				if (self::check_holiday($i, $counter, $year, $month, $day)) {
					$str = 'today-holiday';
				}
			} else if (self::check_holiday($i, $counter, $year, $month, $day)) {
				$str = 'holiday';
			} else {
				$str = "";
			}
			if ($str) {
				echo '<td class="' . $str . '">' . $day . '</td>';
			} else {
				echo '<td>' . $day . '</td>';
			}
			if (($day % 7) == 0) {
				$counter++;
			}
		}

		while ($i < 7) {
			echo '<td>&nbsp;</td>';
			$i++;
		}
		echo "\n" . '</tr></table></div>' . "\n";
	}

	private function check_holiday($i, $counter, $year, $month, $day) {

		$this->options = get_option('shop_calendar');

		/*
			臨時営業日か？
		*/
		$no_holiday_year = !empty($this->options['no_holiday']['year']) ? $this->options['no_holiday']['year'] : '';
		$no_holiday_month = !empty($this->options['no_holiday']['month']) ? $this->options['no_holiday']['month'] : '';
		$no_holiday = !empty($this->options['no_holiday']['day']) ? $this->options['no_holiday']['day'] : '';
		if ($year == $no_holiday_year && $no_holiday_month == $month && $no_holiday == $day) {
			return false;
		}
		$no_holiday_year = !empty($this->options['no_holiday1']['year']) ? $this->options['no_holiday1']['year'] : '';
		$no_holiday_month = !empty($this->options['no_holiday1']['month']) ? $this->options['no_holiday1']['month'] : '';
		$no_holiday = !empty($this->options['no_holiday1']['day']) ? $this->options['no_holiday1']['day'] : '';
		if ($year == $no_holiday_year && $no_holiday_month == $month && $no_holiday == $day) {
			return false;
		}
		$no_holiday_year = !empty($this->options['no_holiday2']['year']) ? $this->options['no_holiday2']['year'] : '';
		$no_holiday_month = !empty($this->options['no_holiday2']['month']) ? $this->options['no_holiday2']['month'] : '';
		$no_holiday = !empty($this->options['no_holiday2']['day']) ? $this->options['no_holiday2']['day'] : '';
		if ($year == $no_holiday_year && $no_holiday_month == $month && $no_holiday == $day) {
			return false;
		}
		$no_holiday_year = !empty($this->options['no_holiday3']['year']) ? $this->options['no_holiday3']['year'] : '';
		$no_holiday_month = !empty($this->options['no_holiday3']['month']) ? $this->options['no_holiday3']['month'] : '';
		$no_holiday = !empty($this->options['no_holiday3']['day']) ? $this->options['no_holiday3']['day'] : '';
		if ($year == $no_holiday_year && $no_holiday_month == $month && $no_holiday == $day) {
			return false;
		}

		if (isset($this->options['holiday'])) {
			$holiday = $this->options['holiday'];
			if (in_array($this->chk_data[$i], $holiday, true)) {
				return true;
			}
		}
		if ($this->options['holiday1st'] != '' && $counter == 0 && $i == $this->options['holiday1st']) {
			return true;
		}
		if ($this->options['holiday2nd'] != '' && $counter == 1 && $i == $this->options['holiday2nd']) {
			return true;
		}
		if ($this->options['holiday3rd'] != '' && $counter == 2 && $i == $this->options['holiday3rd']) {
			return true;
		}
		if ($this->options['holiday4th'] != '' && $counter == 3 && $i == $this->options['holiday4th']) {
			return true;
		}
		if ($this->options['holiday5th'] != '' && $counter == 4 && $i == $this->options['holiday5th']) {
			return true;
		}

		if (intval($this->options['long_holiday1']['year']) == $year) {
			$holiday_month = intval($this->options['long_holiday1']['month']);
			$holiday_start_day = intval($this->options['long_holiday1']['start_day']);
			$holiday_end_day = intval($this->options['long_holiday1']['end_day']);
			if ($holiday_month && $holiday_start_day && $holiday_end_day) {
				if ($month == $holiday_month && $day >= $holiday_start_day && $day <= $holiday_end_day) {
					return true;
				}
			}
		}
		if (intval($this->options['long_holiday2']['year']) == $year) {
			$holiday_month = intval($this->options['long_holiday2']['month']);
			$holiday_start_day = intval($this->options['long_holiday2']['start_day']);
			$holiday_end_day = intval($this->options['long_holiday2']['end_day']);
			if ($holiday_month && $holiday_start_day && $holiday_end_day) {
				if ($month == $holiday_month && $day >= $holiday_start_day && $day <= $holiday_end_day) {
					return true;
				}
			}
		}
		if (intval($this->options['long_holiday3']['year']) == $year) {
			$holiday_month = intval($this->options['long_holiday3']['month']);
			$holiday_start_day = intval($this->options['long_holiday3']['start_day']);
			$holiday_end_day = intval($this->options['long_holiday3']['end_day']);
			if ($holiday_month && $holiday_start_day && $holiday_end_day) {
				if ($month == $holiday_month && $day >= $holiday_start_day && $day <= $holiday_end_day) {
					return true;
				}
			}
		}
		if (intval($this->options['long_holiday4']['year']) == $year) {
			$holiday_month = intval($this->options['long_holiday4']['month']);
			$holiday_start_day = intval($this->options['long_holiday4']['start_day']);
			$holiday_end_day = intval($this->options['long_holiday4']['end_day']);
			if ($holiday_month && $holiday_start_day && $holiday_end_day) {
				if ($month == $holiday_month && $day >= $holiday_start_day && $day <= $holiday_end_day) {
					return true;
				}
			}
		}
		return false;
	}

	public function show_info() {
		$option = get_option('shop_calendar');
		var_dump($option); echo '<br/>';
	}
}

class ShopCalendarWidget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'ShopCalendar',
			'Shop Calendar',
			array('description' => __('店舗向けカレンダー', 'text_domain'), )
		);
	}

	public function widget($args, $instance) {

		global $shop_calendar;
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		if (!empty($title)) {
			echo $before_title . $title . $after_title;
		}
		$options = get_option('shop_calendar');
		if ($shop_calendar) {
			$shop_calendar->show();
		}
		echo $after_widget;
	}

	public function form($instance) {

		$title = ! empty($instance['title']) ? $instance['title'] : __('新しいタイトル', 'text_domain');
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('タイトル:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<?php
	}
	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
}

?>
