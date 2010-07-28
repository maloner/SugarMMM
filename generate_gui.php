<?php

/*********************************************************************************
 * SugarMMM is a tool that automates the creation of MeterMaid scripts for your 
 * SugarCRM application.  
 * SugarCRM, Inc. Copyright (C) 2004-2010 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/


ini_set('memory_limit', '8096M');
if(!defined('sugarEntry'))define('sugarEntry', true);

require_once('metadata/xml_strings.php');

chdir('..');
require_once('include/entryPoint.php');
require_once('include/SugarObjects/VardefManager.php');
if(file_exists('custom/application/Ext/Include/modules.ext.php')) {
	require_once('custom/application/Ext/Include/modules.ext.php');
}
if (file_exists('include/modules_override.php')) {
	require_once('include/modules_override.php');
}
foreach (array("include/language", "custom/Extension/application/Ext/Language") as $dirname) {
	if (!file_exists($dirname)) continue;
	$dir = opendir($dirname);
	while (false !== ($file = readdir($dir))) {
		if (substr($file, -4) == ".php") {
			require_once("$dirname/$file");
		}
	}
}

/**
 * Creates a new HTTP request node as a child of $parent,
 * using the specified action (either POST or GET), with
 * an empty <data> child. Returns the child SimpleXMLElement
 * object.
 * @param SimpleXMLElement $parent
 * @param string $action
 * @param string $name
 */
function createHTTPChild($parent, $action, $name="noname") {
	global $sugar_domain, $sugar_path;
	$node = $parent->addChild('http', '');
	$node->addChild('name', $name);
	$node->addChild('domain', $sugar_domain);
	$node->addChild('path', $sugar_path);
	$node->addChild('method', $action);
	$node->addChild('data', '');
	return $node;
}

/**
 * Adds data variables to $node, $node must be
 * a HTTP request node with a <data> child.
 * $vars must be a map from the "name" attribute
 * to the value.
 * @param SimpleXMLElement $node
 * @param array $vars
 */
function addVars($node, $vars) {
	foreach ($vars as $name=>$val) {
		$var = $node->data->addChild('var', $val);
		$var->addAttribute('name', $name);
	}
}

/**
 * Adds variables to $node (a HTTP request node with a data child) by
 * pulling fields from the current module's vardefs.  Uses metadata arrays
 * to determine what values to add.
 * @param SimpleXMLElement $node
 * @param array $fields
 * @param string $relate_module
 * @param boolean $user_from_popup
 */
function addDataFromFields($node, $fields, $module, $relate_module="none", $user_from_popup=false) {
	global $field_lookup, $type_lookup, $modules_with_email, $app_list_strings;

	if ($user_from_popup) {
		$relate_to_val = $module;
		$relate_id_val = "\${RECORD_NUMBER_g2}";
	} elseif ($relate_module == "none") {
		$relate_to_val = '';
		$relate_id_val = '';
	} else {
		$relate_to_val = $relate_module;
		$relate_id_val = "\${RECORD_NUMBER_g2}";
	}

	$vars = array();
	$vars['relate_to'] = $relate_to_val;
	$vars['relate_id'] = $relate_id_val;

	if (in_array($module, $modules_with_email)) {
		$vars[$module . "_email_widget_id"] = '0';
		$vars[$module . "0emailAddress0"] = 'email${RANDOM_CHAR_g1}@address.com';
		$vars[$module . "0emailAddressVerifiedFlag0"] = 'true';
		$vars[$module . "0emailAddressPrimaryFlag"] = $module . "0emailAddress0";
		$vars[$module . "0emailAddressVerifiedValue0"] = 'email${RANDOM_CHAR_g1}@address.com';
	}

	if ($relate_module != "none") {
		$vars['is_ajax_call'] = '1';
	}

	foreach ($fields as $field=>$info) {
		// try to handle the field by name
		if (in_array($field, array_keys($field_lookup))) {
			if (in_array($relate_module, array_keys($field_lookup[$field]))) {
				$vars[$field] = $field_lookup[$field][$relate_module];
			} else {
				$vars[$field] = $field_lookup[$field]['default'];
			}
			// try to handle the field by type
		} elseif (in_array($info['type'], array_keys($type_lookup))) {
			$vars[$field] = $type_lookup[$info['type']];
		} else {
			// special cases
			switch($info['type']) {
				case 'enum':
					$dropdown_key = "$field" . "_dom";
					// special case hack for quotes module
					// payment terms field
					if (!isset($app_list_strings[$dropdown_key])) {
						$dropdown_key = $field;
					}
					if (isset($app_list_strings[$dropdown_key])) {
						$options = $app_list_strings[$dropdown_key];
						$option_keys = array_keys($options);
						$option_key = $option_keys[mt_rand(0, count($options)-1)];
						$vars[$field] = $options[$option_key];
					}
					break;
				case 'int':
					if (!isset($info['auto_increment'])) {
						$vars[$field] = '1';
					}
					break;
				case 'parent_type':
					if ($relate_module != NULL) {
						$vars['parent_type'] = $relate_to_val;
					} else {
						$vars['parent_type'] = 'Accounts';
					}
					break;
			}
		}
	}

	if ($user_from_popup) {
		$vars['record'] = '${RECORD_NUMBER_g2}';
		$vars['assigned_user_name'] = '${USER_NAME_g4}';
		$vars['assigned_user_id'] = '${USER_ID_g2}';
	} else {
		$vars['assigned_user_name'] = 'Administrator';
		$vars['assigned_user_id'] = '1';
	}

	addVars($node, $vars);
}

/**
 * Adds a regex child node to $node with values
 * specified in $regex_info. $node must  be a HTTP
 * request node.  $regex_info must be a map with
 * entries for each of the required regex children
 * (see the MeterMaid readme).  Returns the regex node.
 * @param SimpleXMLElement $node
 * @param array $regex_info
 */
function addRegex($node, $regex_info) {
	$regex = $node->addChild('regex', '');
	foreach ($regex_info as $nodename=>$val) {
		$regex->addChild($nodename, $val);
	}
	return $regex;
}

/**
 * Adds a controller child node to $node with a
 * throughput percent of $percent.
 * @param unknown_type $node
 * @param unknown_type $percent
 */
function addController($node, $percent) {
	global $testmode;
	if ($testmode) $percent = "100";
	$controller = $node->addChild('controller', '');
	$controller->addChild('name', "$percent percent controller");
	$controller->addChild('precent', $percent);
	return $controller;
}

/**
 * Adds a script child node to $node.  The new
 * script node will have a <file> child for each
 * element in the $filenames array.
 * @param $node
 * @param $filenames
 */
function addScript($node, $filenames) {
	$script = $node->addChild('script', '');
	foreach ($filenames as $filename) {
		$script->addChild('file', $filename);
	}
}

/**
 * Write a file to $gui_writedir named
 * $filename, containing the XML from
 * $node.
 * @param SimpleXMLElement $node
 * @param string $filename
 */
function writeXMLFile($node, $filename) {
	global $gui_writedir;
	$previous_dir = getcwd();
	chdir($gui_writedir);
	$file = fopen($filename, 'w+');
	$result = fwrite($file, $node->asXML());
	if (!$result) {
		fclose($file);
		die("Failed to write $filename\n");
	}
	echo "Successfully wrote $filename<br />";
	fclose($file);
	chdir($previous_dir);
}

function addModuleScripts($root, $module, $scripts) {
   global $beanList, $main_script_gui, $testmode, $timer_delay, $timer_range, $dictionary, $metermaid_path;
   global $cr_script, $qc_script, $ar_script, $quickcreate_relations; 
   
	$object = $beanList[$module];
	VardefManager::loadVardef($module, $object, true);
	$dirstring = "cache/modules/$module";
	if ($object == 'aCase') $object = 'Case';
	$filestring = $object . "vardefs.php";
	chdir($dirstring);
	require_once($filestring);
	chdir('../../..');

	$main_xml = new SimpleXMLElement("<speed></speed>");

	$listview = createHTTPChild($main_xml, 'POST', 'Get module listview');
	$vars = array('module' => $module, 'action' => 'index');
	addVars($listview, $vars);
	$regex_vals = array('name' => 'Random character extractor',
                     'refname' => 'RANDOM_CHAR',
                     'type' => 'body',
                     'matchnum' => '0',
                     'defaultvalue' => 'a',
                     'expression' => '([a-zA-Z0-9])',
                     'template' => '$0$');
	addRegex($listview, $regex_vals);
	$regex_vals = array('name' => 'Random string extractor',
                    'refname' => 'RANDOM_STRING',
                    'type' => 'body',
                    'matchnum' => '0',
                    'defaultvalue' => 'yea right this didnt match',
                    'expression' => '([a-zA-Z0-9]+)',
                    'template' => '$0$');
	addRegex($listview, $regex_vals);

	$search = createHTTPChild($main_xml, 'POST', 'Search for random char');
	$vars = array('searchFormTab' => 'basic_search',
               'module' => $module, 
               'action' => 'index',
               'query' => 'true',
               'name_basic' => '${RANDOM_CHAR_g1}',
               'current_user_only_basic' => '0',
               'button' => 'Search');
	addVars($search, $vars);
	$regex_vals = array('name' => 'Record number extractor',
                     'refname' => 'RECORD_NUMBER',
                     'type' => 'body',
                     'matchnum' => '1',
                     'defaultvalue' => 'NOT_FOUND',
                     'expression' => "(javascript:lvg_nav\('$module', ')([a-zA-Z0-9\-]+)",
                     'template' => '$2$');
	addRegex($search, $regex_vals);
	$regex_vals = array('name' => 'Name extractor',
                     'refname' => 'RECORD_NAME',
                     'type' => 'body',
                     'matchnum' => '1',
                     'defaultvalue' => 'NOT_FOUND',
                     'expression' => "(onFocus=\"javascript:lvg_nav\('$module', ')([a-zA-Z0-9\-]+)(', 'd', 1, this\)\">)([a-zA-Z ]+)",
                     'template' => '$4$');
	addRegex($search, $regex_vals);

	$controller_90 = addController($main_xml, '90');
	$detailview = createHTTPChild($controller_90, 'GET', 'Get record detailview');
	$vars = array('module' => $module,
               'offset' => '1',
               'action' => 'DetailView',
               'record' => '${RECORD_NUMBER_g2}');
	addVars($detailview, $vars);
	$regex_vals = array('name' => 'Subpanel relate module - g2 capitalized, g1 lowercase',
                     'refname' => 'SUBPANEL_RELATE_MODULE',
                     'type' => 'body',
                     'matchnum' => '0',
                     'defaultvalue' => 'Accounts',
                     'expression' => "value=\"Select\"\s\sonclick='open_popup\(\"([a-zA-Z_]+).+child_field\":\"([a-z_]+).+link_field_name\":\"([a-z_]+)\",\"module_name\":\"([a-z_]+)",
                     'template' => '$0$');
   addRegex($detailview, $regex_vals);
   // TODO: remove prefix from filename, or put as metadata
   $selected_scripts = array();
   if (in_array("qc", $scripts)) {
      $selected_scripts[] = "gui-" .$module . "_quickcreate.xml";
   }
   if (in_array("ar", $scripts)) {
      $selected_scripts[] = "gui-" . $module . "_add_relationship.xml";
   }
   addScript($controller_90, $selected_scripts);

	$controller_05 = addController($controller_90, '5');
	$delete = createHTTPChild($controller_05, 'POST', 'Delete record');
	$vars = array('module' => $module,
               'record' => '${RECORD_NUMBER_g2}',
               'return_action' => 'ListView',
               'return_module' => $module,
               'isDuplicate' => 'false',
               'offset' => '1',
               'action' => 'Delete',
               'Delete' => 'Delete');
	addVars($delete, $vars);

	if (in_array("cr", $scripts)) {
		$controller_10 = addController($main_xml, '10');
		// TODO: remove prefix from filename, or put as metadata
		addScript($controller_10, array("gui-" . $module . "_create_record.xml"));
	}

	// TODO: remove prefix from filename, or put as metadata
	$filename = "gui-" . $module . ".xml";
	writeXMLFile($main_xml, $filename);

	//////////////////////////
	// Create record script //
	//////////////////////////
	if (in_array("cr", $scripts)) {
		$module_fields = $dictionary[$object]['fields'];
		$cr_xml = new SimpleXMLElement($cr_script);
      $edit_nodes = $cr_xml->xpath("//data[var='EditView']");
      foreach ($edit_nodes as $edit_node) {
      	$http_parents = $edit_node->xpath("ancestor::http");
         addVars($http_parents[0], array("module" => $module));
      }
		$data_nodes = $cr_xml->xpath("//data[var='Save']");
		foreach ($data_nodes as $data_node) {
			$http_parents = $data_node->xpath("ancestor::http");
			addVars($http_parents[0], array("module" => $module));
			addDataFromFields($http_parents[0], $module_fields, $module); // assuming well-formed MeterMaid XML, data nodes have exactly 1 HTTP ancestor
		}
		// TODO: again with the 'gen2' prefix
		$cr_filename = "gui-" . $module . "_create_record.xml";
		writeXMLFile($cr_xml, $cr_filename);
	}


	/////////////////////////////
	// Quickcreate Script ///////
	/////////////////////////////
	if (in_array("qc", $scripts)) {
		$qc_xml = new SimpleXMLElement($qc_script);
		$qc_module = $quickcreate_relations[mt_rand(0, count($quickcreate_relations) - 1)];
		$qc_object = $beanList[$qc_module];
		$qc_fields = $dictionary[$qc_object]['fields'];
		$form_requests = $qc_xml->xpath("//data[var='Quickcreate']");
		foreach ($form_requests as $form_request) {
		   $http_parents = $form_request->xpath("ancestor::http");
		   addVars($http_parents[0], array('module' => $qc_module));
		}
		$data_nodes = $qc_xml->xpath("//data[var='Save']");
		foreach ($data_nodes as $data_node) {
		   $http_parents = $data_node->xpath("ancestor::http");
		   addVars($http_parents[0], array('module' => $qc_module));
		   addDataFromFields($http_parents[0], $qc_fields, $qc_module, $module);
		}
		// TODO: remove prefix
		$qc_filename = "gui-" . $module . "_quickcreate.xml";
		writeXMLFile($qc_xml, $qc_filename);
	}

	/////////////////////////////
	// Add relationship Script //
	/////////////////////////////
	if (in_array("ar", $scripts)) {
		$ar_xml = new SimpleXMLElement($ar_script);
		$save_datanodes = $ar_xml->xpath("//data[var='Save2']");
		foreach ($save_datanodes as $datanode) {
		   $parents = $datanode->xpath("ancestor::http");
		   addVars($parents[0], array('module' => $module, 'return_module' => $module));
		}
		// TODO: remove prefix
		$ar_filename = "gui-" . $module . "_add_relationship.xml";
		writeXMLFile($ar_xml, $ar_filename);
	}
	
	return $filename;
}

$selected_modules = array();
$selected_files = array();
foreach ($_POST as $key=>$val) {
   $prefix = substr($key, 0, 2);
   if (in_array($prefix, array("cr", "ar", "qc"))) {
      $mod = substr($key, 3);
      $script = translate($prefix);
      if (!array_key_exists($mod, $selected_modules)) {
      	$selected_modules[$mod] = array();
      }
      $selected_modules[$mod][] = $script;
   } elseif (substr($key, 0, 9) == "selected_") {
   	$filename = substr($key, 9);
   	$selected_files[] = substr($filename, 0, -4) . ".xml"; // PHP replaces "." with "_" in form data
   }
}

foreach ($quickcreate_relations as $relation_module) {
	$relation_object = $beanList[$relation_module];
	VardefManager::loadVardef($relation_module, $relation_object);
	$dirstring = "cache/modules/$relation_module";
	$filestring = $relation_object . "vardefs.php";
	chdir($dirstring);
	require_once($filestring);
	chdir('../../..');
}

echo "<html>";
echo "<title>Sugar MMM (MeterMaid Maker)</title>";
$style = <<<STL
<style type="text/css">
body {
   margin: 10px;
   font-family: Monospace;
   font-size: 14px;
}
</style>
STL;
echo $style;
echo "<body>";
echo "<h1 style=\"text-align: center\">Sugar MMM (MeterMaid Maker)</h1>";

$root = new SimpleXMLElement($root_script);
$root->testinfo->name = "GUI generated test";
$root->testinfo->assertlog = "/tmp/guigen.log";

$threads = $_POST['num_threads'];
if (!is_numeric($threads)) {
   $threads = "1";
}
$root->testinfo->numthreads = $threads;

if ($testmode) {
   $root->timer->delay = "0";
   $root->timer->range = "0";
} else {
   $root->timer->delay = $timer_delay;
   $root->timer->range = $timer_range;
}
$files_written = array();

foreach ($selected_modules as $mod=>$scripts) {
	$files_written[] = addModuleScripts($root, $mod, $scripts);
}

addScript($root, $files_written);
addScript($root, $selected_files);
if ($_POST['root_filename'] == "") {
	$root_filename = "gui-root.xml";
} else {
	$root_filename = $_POST['root_filename'];
}
writeXMLFile($root, $root_filename);

echo "<br /><br /><a href='sugarmmm_gui.php'>Generate more scripts</a>";

echo "</body></html>";

?>
