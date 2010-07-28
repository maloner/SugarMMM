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

<body>
<h1 style="text-align: center">Sugar MMM (MeterMaid Maker)</h1>
<p>You selected the following scripts:</p> <br />
<?php
function translate($script_name) {
   switch ($script_name) {
   case "ar": return "Add relationship";
   case "cr": return "Create record";
   case "qc": return "Quick create";
   default: return "";
   }
}

foreach ($_POST as $key=>$val) {
   if ($val == '1') {
      $module_name = substr($key, 3);
      $script_name = translate(substr($key, 0, 2));
      echo "$module_name: $script_name <br />";
   }
}
?>
<br />
<?php
require_once('metadata/xml_strings.php');
$dir = opendir($gui_writedir);
echo "<p>The following scripts were detected at $gui_writedir. Click to select: <br />" .
"<i>Note: you can change your script directory by modifying metadata/xml_strings.php</i><p>";
echo "<form action=\"sugarmmm_gui3.php\" method=\"POST\">";
while (false !== ($file = readdir($dir))) {
   if (substr($file, -4) == ".xml") {
      echo "<input type=\"checkbox\" name=\"selected_$file\" value=\"$file\" /> $file<br />";
   }
}
foreach ($_POST as $key=>$val) {
   if ($val == '1') {
      echo "<input type='hidden' name='$key' value='$val' />";
   }
}
?>
<input type="button" value="Start over" onclick="window.location='sugarmmm_gui.php'"/>
<input type="submit" value="Continue" />
</form>
</body>
</html>
