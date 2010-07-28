<!--

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
 
 -->

<html>

<title>Sugar MMM (MeterMaid Maker)</title>

<style type="text/css">
body 
{ 
   margin: 10px;
   font-family: Monospace;
   font-size: 14px;
}
</style>

<head>
<!-- Required CSS -->
<link type="text/css" rel="stylesheet"
	href="http://yui.yahooapis.com/2.8.1/build/treeview/assets/skins/sam/treeview.css">
<!-- Optional CSS for for date editing with Calendar-->
<link type="text/css" rel="stylesheet"
	href="http://yui.yahooapis.com/2.8.1/build/calendar/assets/skins/sam/calendar.css">

<!-- Dependency source file -->
<script
	src="http://yui.yahooapis.com/2.8.1/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<!-- Optional dependency source file -->
<script
	src="http://yui.yahooapis.com/2.8.1/build/animation/animation-min.js"
	type="text/javascript"></script>
<!-- Optional dependency source file for date editing with Calendar-->
<script
	src="http://yui.yahooapis.com/2.8.1/build/calendar/calendar-min.js"></script>
<!-- Optional dependency source file to decode contents of yuiConfig markup attribute-->
<script src="http://yui.yahooapis.com/2.8.1/build/json/json-min.js"></script>

<!-- TreeView source file -->
<script
	src="http://yui.yahooapis.com/2.8.1/build/treeview/treeview-min.js"></script>
</head>

<body class="yui-skin-sam">
<h1 style="text-align:center">Sugar MMM (MeterMaid Maker)</h1>
<p>Select modules & scripts:</p>
<div id="treeDiv1" class="whitebg ygtv-checkbox"></div>
<form action="sugarmmm_gui2.php" method="POST">
   <?php
   if(!defined('sugarEntry'))define('sugarEntry', true);
   chdir('..');
   require_once('include/entryPoint.php');
   require_once('metadata/xml_strings.php');
   foreach ($GLOBALS['moduleList'] as $candidate_module) {
      if (!in_array($candidate_module, $modules) && !in_array($candidate_module, $bad_modules)) {
         $modules[] = $candidate_module;
      }
   }
   foreach ($modules as $m) {
      echo "<input type='hidden' id='cr_$m' name='cr_$m' value='' />\n";
      echo "<input type='hidden' id='ar_$m' name='ar_$m' value='' />\n";
      echo "<input type='hidden' id='qc_$m' name='qc_$m' value='' />\n";
   }
   ?>
   <input type="submit" value="Continue" id="continue" />
</form>
<script type="text/javascript">
<?php
foreach ($GLOBALS['moduleList'] as $candidate_module) {
   if (!in_array($candidate_module, $modules) && !in_array($candidate_module, $bad_modules)) {
      $modules[] = $candidate_module;
   }
}
$toEcho = <<<ECH

var tree;
function init() {
   tree = new YAHOO.widget.TreeView("treeDiv1");
   //var root = tree.getRoot();
   var root = new YAHOO.widget.TextNode('Select All', tree.getRoot(), true);
ECH;
foreach ($modules as $mod) {
   $toEcho .= "var mod = new YAHOO.widget.TextNode('$mod', root, false);\n";
   $toEcho .= "var crChild = new YAHOO.widget.TextNode({label: 'Create Record', expanded: false, module: '$mod', script: 'cr'}, mod);\n";
   $toEcho .= "var arChild = new YAHOO.widget.TextNode({label: 'Add relationship', expanded: false, module: '$mod', script: 'ar'}, mod);\n";
   $toEcho .= "var qcChild = new YAHOO.widget.TextNode({label: 'Quick create', expanded: false, module: '$mod', script: 'qc'}, mod);\n";
}
$toEcho .= <<<ECH
tree.setNodesProperty('propagateHighlightUp',true);
tree.setNodesProperty('propagateHighlightDown',true);
tree.subscribe('clickEvent',tree.onEventToggleHighlight);
tree.render();
}
YAHOO.util.Event.onDOMReady(init);

ECH;
echo $toEcho;
?>
YAHOO.util.Event.on('continue','click',function() {
	var hiLit = tree.getNodesByProperty('highlightState',1);
	if (!YAHOO.lang.isNull(hiLit)) {
		for (var i = 0; i < hiLit.length; i++) {
			var current = hiLit[i].data;
			var input = document.getElementById(current.script + "_" + current.module);
			if (!YAHOO.lang.isNull(input)) {
				input.value = "1";
			}
		}
	} 
})
</script>
<a href='README.html'>View Readme</a>
</body>
</html>
