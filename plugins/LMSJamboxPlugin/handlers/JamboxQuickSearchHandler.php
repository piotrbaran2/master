<?php

/*
 *  LMS version 1.11-git
 *
 *  Copyright (C) 2001-2016 LMS Developers
 *
 *  Please, see the doc/AUTHORS for more information about authors!
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 *  $Id$
 */

/**
 * QuickSearchHandler
 *
 * @author Tomasz Chiliński <tomasz.chilinski@chilan.com>
 */
class JamboxQuickSearchHandler {
	public function quicksearchAfterSubmit(array $hook_data) {
		global $LMSTV;

		$DB = LMSDB::getInstance();

		$SESSION = $hook_data['session'];
		$mode = $hook_data['mode'];
		$search = $hook_data['search'];
		$sql_search = $hook_data['sql_search'];

		if ($mode == 'stb') {
			if (isset($_GET['ajax'])) {
				$list = $LMSTV->StbGetRegistered('customer');

				$eglible = array(); $actions = array(); $descriptions = array();

				if (!empty($list))
					foreach ($list as $row)
						if (strpos(strtoupper($row['stb_mac']), strtoupper($search)) !== false && count($eglible) < 20) {
							$customer = $DB->GetRow("SELECT id, (" . $DB->Concat('lastname', "' '", 'name') . ") AS name
								FROM customerview WHERE tv_cust_number = ?",
								array($row['cust_number']));
							$actions[$row['id']] = '?m=customerinfo&id=' . $customer['id'] . '#tvaccountbox';
							$eglible[$row['id']] = escape_js($row['stb_mac']);
							$descriptions[$row['id']] = escape_js($customer['name']);
						}
				header('Content-type: text/plain');
				if ($eglible) {
					print preg_replace('/$/', "\");\n","this.eligible = new Array(\"" . implode('","', $eglible));
					print preg_replace('/$/', "\");\n","this.descriptions = new Array(\"" . implode('","', $descriptions));
					print preg_replace('/$/', "\");\n","this.actions = new Array(\"" . implode('","', $actions));
				} else {
					print "false;\n";
				}
				exit;
			}

/*
			$s['name'] = $search;

			$SESSION->save('v_nodesearch', $s);
			$SESSION->save('nslk', 'OR');
			$SESSION->remove('nslp');

			$hook_data['target'] = '?m=tvstbsearch';
*/
		}

		return $hook_data;
	}
}

?>
