<?php
/**
 * @package     VikRentCar
 * @subpackage  com_vikrentcar
 * @author      Alessio Gaggii - e4j - Extensionsforjoomla.com
 * @copyright   Copyright (C) 2018 e4j - Extensionsforjoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

defined('ABSPATH') or die('No script kiddies please!');

jimport('joomla.application.component.view');

class VikrentcarViewPromotions extends JViewVikRentCar {
	function display($tpl = null) {
		VikRentCar::prepareViewContent();
		$dbo = JFactory::getDbo();
		$vrc_tn = VikRentCar::getTranslator();
		$pshowcars = VikRequest::getInt('showcars', 1, 'request');
		$pshowcars = $pshowcars == 1 ? 1 : 0;
		$pmaxdate = VikRequest::getInt('maxdate', 6, 'request');
		$plim = VikRequest::getInt('lim', 10, 'request');
		$promotions = array();
		$cars = array();
		$ind = 0;
		$q = "SELECT * FROM `#__vikrentcar_seasons` WHERE `promo`=1;";
		$dbo->setQuery($q);
		$dbo->execute();
		if ($dbo->getNumRows() > 0) {
			$all_promotions = $dbo->loadAssocList();
			$vrc_tn->translateContents($all_promotions, '#__vikrentcar_seasons');
			$base_year = (int)date('Y');
			$base_ts = time();
			$base_month = (int)date('n');
			foreach ($all_promotions as $k => $promo) {
				$promo_year = !empty($promo['year']) && $promo['year'] > 0 ? $promo['year'] : $base_year;
				$promo_from_ts = ((int)mktime(0, 0, 0, 1, 1, $promo_year)) + $promo['from'];
				$promo_to_ts = ((int)mktime(0, 0, 0, 1, 1, $promo_year)) + $promo['to'];
				if ($promo_year % 4 == 0 && ($promo_year % 100 != 0 || $promo_year % 400 == 0)) {
					//leap year
					$leapts = mktime(0, 0, 0, 2, 29, $promo_year);
					if ($promo_from_ts >= $leapts) {
						$promo_from_ts += 86400;
					}
				}
				if ($base_ts > $promo_from_ts && $base_ts > $promo_to_ts) {
					if (empty($promo['year'])) {
						$promo_year++;
						$promo_from_ts = ((int)mktime(0, 0, 0, 1, 1, $promo_year)) + $promo['from'];
						if ($promo_year % 4 == 0 && ($promo_year % 100 != 0 || $promo_year % 400 == 0)) {
							//leap year
							$leapts = mktime(0, 0, 0, 2, 29, $promo_year);
							if ($promo_from_ts >= $leapts) {
								$promo_from_ts += 86400;
							}
						}
					} else {
						//Start ts is in the past, not tied to the year, check if season end ts is in the future
						$check_promo_to_ts = ((int)mktime(0, 0, 0, 1, 1, ($promo['from'] > $promo['to'] ? ($promo_year + 1) : $promo_year))) + $promo['to'];
						if ($base_ts > $check_promo_to_ts) {
							continue;
						}
					}
				}
				if ($promo['from'] > $promo['to']) {
					$promo_year++;
				}
				$promo_to_ts = ((int)mktime(0, 0, 0, 1, 1, $promo_year)) + $promo['to'];
				if ($promo_year % 4 == 0 && ($promo_year % 100 != 0 || $promo_year % 400 == 0)) {
					//leap year
					$leapts = mktime(0, 0, 0, 2, 29, $promo_year);
					if ($promo_to_ts >= $leapts) {
						$promo_to_ts += 86400;
					}
				}
				if ($promo_from_ts < $promo_to_ts) {
					//Begin: Check Max Date in the Future (Months)
					$promo_from_month = (int)date('n', $promo_from_ts);
					$promo_from_year = (int)date('Y', $promo_from_ts);
					if ($base_year == $promo_from_year) {
						//Same Year
						$months_diff = $promo_from_month - $base_month;
						if ($months_diff > $pmaxdate) {
							continue;
						}
					} else {
						//Different Year
						$promo_from_month += 12 * ($promo_from_year - $base_year);
						$months_diff = $promo_from_month - $base_month;
						if ($months_diff > $pmaxdate) {
							continue;
						}
					}
					//End: Check Max Date in the Future (Months)
					$promotions[$ind] = $all_promotions[$k];
					$promotions[$ind]['promo_from_ts'] = $promo_from_ts;
					$promotions[$ind]['promo_to_ts'] = $promo_to_ts;
					$promotions[$ind]['promo_valid_ts'] = $promo_from_ts;
					//set valid until to end ts in case of 0 days in advance
					if (empty($promo['promodaysadv']) || !($promo['promodaysadv'] > 0)) {
						$promotions[$ind]['promo_valid_ts'] = $promo_to_ts;
					} elseif (!empty($promo['promodaysadv']) && $promo['promodaysadv'] > 0) {
						$dst_from_ts = date('I', $promo_from_ts);
						$valid_ts = $promo_from_ts - (86400 * $promo['promodaysadv']);
						$dst_valid_ts = date('I', $valid_ts);
						if ($dst_from_ts != $dst_valid_ts) {
							if ($dst_valid_ts) {
								$valid_ts -= 3600;
							} else {
								$valid_ts += 3600;
							}
						}
						$promotions[$ind]['promo_valid_ts'] = $valid_ts;
					}
					$ind++;
				}
			}
			if (count($promotions) > 0) {
				$promo_map = array();
				$sorted = array();
				$promos_cars = array();
				foreach ($promotions as $k => $v) {
					$promo_map[$k] = $v['promo_from_ts'];
					$allcars = explode(",", $v['idcars']);
					foreach ($allcars as $idcar) {
						$idcar = intval(str_replace("-", "", trim($idcar)));
						if ($idcar > 0) {
							$promos_cars[$idcar] = $idcar;
						}
					}
				}
				asort($promo_map);
				foreach ($promo_map as $k => $v) {
					$sorted[$k] = $promotions[$k];
				}
				$promotions = $sorted;
				if (count($promos_cars) > 0) {
					$q = "SELECT * FROM `#__vikrentcar_cars` WHERE `id` IN(".implode(",", $promos_cars).") ORDER BY `#__vikrentcar_cars`.`name` ASC;";
					$dbo->setQuery($q);
					$dbo->execute();
					if ($dbo->getNumRows() > 0) {
						$fetch_cars = $dbo->loadAssocList();
						$vrc_tn->translateContents($fetch_cars, '#__vikrentcar_cars');
						foreach ($fetch_cars as $v) {
							$cars[$v['id']] = $v;
						}
					}
				}
			}
		}
		
		$this->promotions = $promotions;
		$this->cars = $cars;
		$this->showcars = $pshowcars;
		$this->vrc_tn = $vrc_tn;
		//theme
		$theme = VikRentCar::getTheme();
		if ($theme != 'default') {
			$thdir = VRC_SITE_PATH.DS.'themes'.DS.$theme.DS.'promotions';
			if (is_dir($thdir)) {
				$this->_setPath('template', $thdir.DS);
			}
		}
		//
		parent::display($tpl);
	}
}
