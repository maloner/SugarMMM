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

// Path  and domain of the Sugar instance for HTTP requests
$sugar_path = "";
$sugar_domain = "localhost";

// Local system absolute path to MeterMaid, for command-line version
$metermaid_path = "";

// Path to directory where GUI version will write files
// The web server must have permission to write to this directory
$gui_writedir = "";

// Timer delay and range
$timer_delay = "40000";
$timer_range = "20000";

// Login username and password
$login_name = "";
$login_pw = "";

// Base modules supported
$modules = array('Accounts', 'Opportunities', 'Contacts', 'Leads', 'Calls', 'Meetings'
, 'Tasks', 'Notes', 'Quotes', 'Cases', 'Bugs', 'Products');

// Modules not supported
$bad_modules = array("Home", "Activities", "Reports", "Emails", "Documents",
"Project", "Forecasts", "Contracts", "KBDocuments", "SugarFavorites", "Campaigns");

// The "quickcreate" script will target one of the modules in this array, trying
// to create new records related to the current module.
$quickcreate_relations = array("Contacts", "Opportunities", "Leads", "Calls", "Meetings");

// If test mode is on, all througput controllers are set to 100%, 
// and the timer's delay/range is set to zero.
$testmode = true;

// For a save request, the script extracts a module's fields from the
// vardefs.  Certain fields are handled by name, and the rest by type.
$field_lookup = array('currency_id' => array('default' => '-99'),
   'account_id' => array('default' => '${MEMBER_OF_ID_g2}', 'Accounts' => '${RECORD_NUMBER_g2}'),
   'shipping_account_id' => array('default' => '${MEMBER_OF_ID_g2}', 'Accounts' => '${RECORD_NUMBER_g2}'),
   'billing_account_id' => array('default' => '${MEMBER_OF_ID_g2}', 'Accounts' => '${RECORD_NUMBER_g2}'),
   'account_name' => array('default' => '${MEMBER_OF_NAME_g4}', 'Accounts' => '${RECORD_NAME_g4}'),
   'shipping_account_name' => array('default' => '${MEMBER_OF_NAME_g4}', 'Accounts' => '${RECORD_NAME_g4}'),
   'billing_account_name' => array('default' => '${MEMBER_OF_NAME_g4}', 'Accounts' => '${RECORD_NAME_g4}'),
   'parent_name' => array('default' => '${RECORD_NAME_g4}',  'none' => '${MEMBER_OF_NAME_g4}'),
   'campaign_name' => array('default' => '${CAMPAIGN_NAME_g4}'),
   'report_to_name' => array('default' => '${CONTACT_NAME_g4}'),
   'contact_name' => array('default' => '${CONTACT_NAME_g4}'),
   'shipping_contact_name' => array('default' => '${CONTACT_NAME_g4}'),
   'billing_contact_name' => array('default' => '${CONTACT_NAME_g4}'),
   'contact_id' => array('default' => '${CONTACT_ID_g2}'),
   'shipping_contact_id' => array('default' => '${CONTACT_ID_g2}'),
   'billing_contact_id' => array('default' => '${CONTACT_ID_g2}'),
   'opportunity_name' => array('default' => '${OPPORTUNITY_NAME_g4}'),
   'parent_id' => array('default' => '${RECORD_NUMBER_g2}', 'none' => '${MEMBER_OF_ID_g2}'),
   'campaign_id' => array('default' => '${CAMPAIGN_ID_g2}'),
   'reports_to_id' => array('default' => '${CONTACT_ID_g2}'),
   'contact_id' => array('default' => '${CONTACT_ID_g2}'),
   'shipping_contact_id' => array('default' => '${CONTACT_ID_g2}'),
   'billing_contact_id' => array('default' => '${CONTACT_ID_g2}'),
   'opportunity_id' => array('default' => '${OPPORTUNITY_ID_g2}'),
   'date_start' => array('default' => '12/30/2013 01:00pm'),
   'date_due_flag' => array('default' => '1'),
);

$type_lookup = array(
   'name' => '${RANDOM_STRING_g1}',
   'text' => '${RANDOM_STRING_g1}',
   'varchar' => '${RANDOM_STRING_g1}',
   'phone' => '12345678901',
   'url' => 'http://www.website.com',
   'currency' => '100',
   'date' => '07/16/2013',
   'datetimecombo' => '12/31/2013 02:00pm',
   'bool' => '0'
);

// Modules in this array have email addresses associated with them and use
// the email address widget.
$modules_with_email = array('Accounts', 'Contacts', 'Leads');

// Main script skeleton, containing the login request
$main_script = <<<MAIN
<speed>
   <testinfo>
      <name></name>
      <assertlog></assertlog>
      <asserttype>AssertionVisualizer</asserttype>
   </testinfo>
   <timer>
      <delay></delay>
      <range></range>
   </timer>
   <http>
      <name>Log in</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>POST</method>
      <data>
         <var name="module">Users</var>
         <var name="action">Authenticate</var>
         <var name="return_module">Users</var>
         <var name="return_action">Login</var>
         <var name="user_name">$login_name</var>
         <var name="user_password">$login_pw</var>
         <var name="Login">Log+In</var>
      </data>
   </http>
   <!-- end template xml -->
   <!-- begin generated xml -->
</speed>
MAIN;
;

// Skeleton of create record script.
$cr_script = <<<CRS
<speed>
<http>
      <name>Get 'Member Of' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Accounts</var>
         <var name="action">Popup</var>
         <var name="mode">Single</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find account name for 'member of' popup</name>
         <refname>MEMBER_OF_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>MEMBER_OF_NAME_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Accounts',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find account id 'member of' popup</name>
         <refname>MEMBER_OF_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>MEMBER_OF_ID_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Accounts',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
   <http>
      <name>Get 'Campaign' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Campaigns</var>
         <var name="action">Popup</var>
         <var name="mode">Single</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find campaign name for 'campaigns' popup</name>
         <refname>CAMPAIGN_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>CAMPAIGN_NAME_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Campaigns',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find campaign id for 'campaigns' popup</name>
         <refname>CAMPAIGN_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>CAMPAIGN_ID_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Campaigns',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
   <http>
      <name>Get 'Contacts' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Contacts</var>
         <var name="action">Popup</var>
         <var name="mode">Single</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find contact name</name>
         <refname>CONTACT_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>CONTACT_NAME_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Contacts',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find contact id</name>
         <refname>CONTACT_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>CONTACT_ID_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Contacts',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
   <http>
      <name>Get 'Teams' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Teams</var>
         <var name="action">Popup</var>
         <var name="mode">MULTISELECT</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find team name</name>
         <refname>TEAM_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>Global</defaultvalue>
         <expression>(onclick="send_team_to_form\('Teams',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z0-9]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find team id</name>
         <refname>TEAM_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>1</defaultvalue>
         <expression>(onclick="send_team_to_form\('Teams',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z0-9]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
   <http>
      <name>Get 'Opportunities' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Opportunities</var>
         <var name="action">Popup</var>
         <var name="mode">Single</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find opportunity name</name>
         <refname>OPPORTUNITY_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>OPPORTUNITY_NAME_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Opportunities',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find opportunity id</name>
         <refname>OPPORTUNITY_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>OPPORTUNITY_ID_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Opportunities',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
<http>
   <name>Get module EditView</name>
   <domain>$sugar_domain</domain>
   <path>$sugar_path</path>
   <method>GET</method>
   <data>
      <var name="action">EditView</var>
   </data>
</http>
<http>
   <name>Perform save request</name>
   <domain>$sugar_domain</domain>
   <path>$sugar_path</path>
   <method>POST</method>
   <data>
	   <var name="isDuplicate">false</var>
	   <var name="action">Save</var>
	   <var name="offset">1</var>
	   <var name="button">Save</var>
	   <var name="emailAddressWidget">1</var>
	   <var name="useEmailWidget">true</var>
	   <var name="team_name_new_on_update">false</var>
	   <var name="team_name_allow_new">true</var>
	   <var name="team_name">team_name</var>
	   <var name="team_name_field">team_name_table</var>
	   <var name="arrow_team_name">show</var>
	   <var name="team_name_collection_0">\${TEAM_NAME_g4}</var>
	   <var name="id_team_name_collection_0">\${TEAM_ID_g2}</var>
	   <var name="primary_team_name_collection">0</var>
	   <var name="full_form">full_form</var>
   </data>
 </http>
</speed>
CRS;
;

// Skeleton of add relationship script
$percent = $testmode ? "100" : "10"; 
$ar_script = <<<ARS
<speed>
<controller>
<name>10percent</name>
<precent>$percent</precent>
<http>
   <name>Request module popup form</name>
   <domain>$sugar_domain</domain>
   <path>$sugar_path</path>
   <method>GET</method>
   <data>
      <var name="module">\${SUBPANEL_RELATE_MODULE_g1}</var>
      <var name="action">Popup</var>
      <var name="hide_clear_button">true</var>
      <var name="mode">MultiSelect</var>
      <var name="create">true</var>
      <var name="metadata">undefined</var>
   </data>
   <regex>
      <name>Search field name extractor</name>
      <refname>SEARCH_FIELD</refname>
      <type>body</type>
      <matchnum>1</matchnum>
      <defaultvalue>SEARCH_FIELD_ERROR</defaultvalue>
      <expression>(input type='text' name=')([a-zA-Z0-9\-_]+)</expression>
      <template>$2$</template>
   </regex>
</http>
<http>
   <name>Search</name>
   <domain>$sugar_domain</domain>
   <path>$sugar_path</path>
   <method>POST</method>
   <data>
      <var name="\${SEARCH_FIELD_g2}">\${RANDOM_CHAR_g1}</var>
      <var name="module">\${SUBPANEL_RELATE_MODULE_g1}</var>
      <var name="action">Popup</var>
      <var name="query">true</var>
      <var name="populate_parent">false</var>
      <var name="hide_clear_button">true</var>
      <var name="button">Search</var>
   </data>
   <regex>
      <name>Found ID regex</name>
      <refname>FOUND_ID</refname>
      <type>body</type>
      <matchnum>1</matchnum>
      <defaultvalue>1</defaultvalue>
      <expression>(onclick="send_back\('[a-zA-Z]+',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z ]+)</expression>
      <template>$2$</template>
   </regex>
</http>
<http>
   <name>Perform save request</name>
   <domain>$sugar_domain</domain>
   <path>$sugar_path</path>
   <method>POST</method>
   <data>
      <var name="subpanel_id">\${FOUND_ID_g2}</var>
      <var name="value">DetailView</var>
      <var name="http_method">get</var>
      <var name="return_id">\${RECORD_NUMBER_g2}</var>
      <var name="record">\${RECORD_NUMBER_g2}</var>
      <var name="isDuplicate">false</var>
      <var name="action">Save2</var>
      <var name="inline">1</var>
      <var name="select_entire_list">0</var>
      <var name="child_field">\${SUBPANEL_RELATE_MODULE_g2}</var>
      <var name="subpanel_module_name">\${SUBPANEL_RELATE_MODULE_g4}</var>
      <var name="subpanel_field_name">\${SUBPANEL_RELATE_MODULE_g3}</var>
      <var name="refresh_page">0</var>
   </data>
</http>
</controller>
</speed>
ARS;
;

// Skeleton of quickcreate script
$percent = $testmode ? "100" : "5";
$qc_script = <<<QCS
<speed> 
   <controller>
      <name>5percent</name>
      <precent>$percent</precent>
      
      <http>
      <name>Get 'Member Of' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Accounts</var>
         <var name="action">Popup</var>
         <var name="mode">Single</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find account name for 'member of' popup</name>
         <refname>MEMBER_OF_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>MEMBER_OF_NAME_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Accounts',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find account id 'member of' popup</name>
         <refname>MEMBER_OF_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>MEMBER_OF_ID_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Accounts',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
   <http>
      <name>Get 'Campaign' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Campaigns</var>
         <var name="action">Popup</var>
         <var name="mode">Single</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find campaign name for 'campaigns' popup</name>
         <refname>CAMPAIGN_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>CAMPAIGN_NAME_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Campaigns',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find campaign id for 'campaigns' popup</name>
         <refname>CAMPAIGN_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>CAMPAIGN_ID_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Campaigns',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
   <http>
      <name>Get 'Contacts' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Contacts</var>
         <var name="action">Popup</var>
         <var name="mode">Single</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find contact name</name>
         <refname>CONTACT_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>CONTACT_NAME_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Contacts',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find contact id</name>
         <refname>CONTACT_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>CONTACT_ID_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Contacts',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
   <http>
      <name>Get 'Teams' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Teams</var>
         <var name="action">Popup</var>
         <var name="mode">MULTISELECT</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find team name</name>
         <refname>TEAM_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>Global</defaultvalue>
         <expression>(onclick="send_team_to_form\('Teams',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z0-9]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find team id</name>
         <refname>TEAM_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>1</defaultvalue>
         <expression>(onclick="send_team_to_form\('Teams',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z0-9]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
   <http>
      <name>Get 'Opportunities' Popup</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>GET</method>
      <data>
         <var name="module">Opportunities</var>
         <var name="action">Popup</var>
         <var name="mode">Single</var>
         <var name="create">true</var>
         <var name="metadata">undefined</var>
      </data>
      <regex>
         <name>Find opportunity name</name>
         <refname>OPPORTUNITY_NAME</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>OPPORTUNITY_NAME_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Opportunities',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$4$</template>
      </regex>
      <regex>
         <name>Find opportunity id</name>
         <refname>OPPORTUNITY_ID</refname>
         <type>body</type>
         <matchnum>1</matchnum>
         <defaultvalue>OPPORTUNITY_ID_ERROR</defaultvalue>
         <expression>(onclick="send_back\('Opportunities',')([a-zA-Z0-9\-]+)('\);">)([a-zA-Z]+)</expression>
         <template>$2$</template>
      </regex>
   </http>
      
      <http>
         <name>Request quickcreate form</name>
         <domain>$sugar_domain</domain>
         <path>$sugar_path</path>
         <method>GET</method>
         <data>
            <var name="action">Quickcreate</var>
            <var name="record">\${RECORD_NUMBER_g2}</var>
         </data>
      </http>
      <http>
         <name>Perform save request</name>
		   <domain>$sugar_domain</domain>
		   <path>$sugar_path</path>
		   <method>POST</method>
		   <data>
			   <var name="isDuplicate">false</var>
			   <var name="action">Save</var>
			   <var name="offset">1</var>
			   <var name="button">Save</var>
			   <var name="emailAddressWidget">1</var>
			   <var name="useEmailWidget">true</var>
			   <var name="team_name_new_on_update">false</var>
			   <var name="team_name_allow_new">true</var>
			   <var name="team_name">team_name</var>
			   <var name="team_name_field">team_name_table</var>
			   <var name="arrow_team_name">show</var>
			   <var name="team_name_collection_0">\${TEAM_NAME_g4}</var>
			   <var name="id_team_name_collection_0">\${TEAM_ID_g2}</var>
			   <var name="primary_team_name_collection">0</var>
			   <var name="full_form">full_form</var>
         </data>
      </http>   
   </controller>
</speed>
QCS;
;

// Skeleton of root script (for gui mode)
// Contains only the "testinfo" element, timer, and
// the login request
$root_script = <<<ROOT
<speed>
   <testinfo>
      <name></name>
      <assertlog></assertlog>
      <asserttype>AssertionVisualizer</asserttype>
   </testinfo>
   <timer>
      <delay></delay>
      <range></range>
   </timer>
   <http>
      <name>Log in</name>
      <domain>$sugar_domain</domain>
      <path>$sugar_path</path>
      <method>POST</method>
      <data>
         <var name="module">Users</var>
         <var name="action">Authenticate</var>
         <var name="return_module">Users</var>
         <var name="return_action">Login</var>
         <var name="user_name">$login_name</var>
         <var name="user_password">$login_pw</var>
         <var name="Login">Log+In</var>
      </data>
   </http>
   <!-- end template xml -->
   <!-- begin generated xml -->
</speed>
ROOT;
;
