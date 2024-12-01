<?php
/**
 * Plugin Name: Table Sorter
 * Plugin URI: https://wpreloaded.com/plugins/table-sorter
 * Description: This plugin makes your standard HTML tables sortable. For more details, check plugin's docs.
 * Version: 2.3
 * Author: Farhan Noor
 * Author URI: https://linkedin.com/in/farhan-noor
 * License: GPLv2 or later
 */

function tablesorter_enque_scripts(){
	wp_register_script('table-sorter',plugins_url('table-sorter/jquery.tablesorter.min.js'),array('jquery'));
	wp_enqueue_script('table-sorter-metadata',plugins_url('table-sorter/jquery.metadata.js'),array('table-sorter'), '2.2');
	wp_enqueue_script('table-sorter-custom-js',plugins_url('table-sorter/wp-script.js'),array('table-sorter'), '2.2');
	wp_enqueue_style('table-sorter-custom-css',plugins_url('table-sorter/wp-style.css'));        
}
add_action( 'wp_enqueue_scripts', 'tablesorter_enque_scripts' );
if( get_option('tablesorter_admin') )    add_action( 'admin_enqueue_scripts', 'tablesorter_enque_scripts' );

function tablesorter_register_settings(){
    register_setting('tablesorter_settings_group', 'tablesorter_admin', array('type' => 'boolean'));
}
add_action('admin_init', 'tablesorter_register_settings');

function tablesorter_settings(){
    echo '<form class="wrap" action="options.php" method="post">';
    settings_fields( 'tablesorter_settings_group' ); 
    do_settings_sections( 'tablesorter_settings_group' );
    echo '<h1>Table Sorter</h1>';
    if(get_option('tablesorter_admin') ){
        $yes = "checked";
        $no = NULL;
    } else {
        $yes = NULL;
        $no = "checked";
    }
    echo '<p><span class="label">Sorte Tables in WordPress Admin &nbsp; </span> ';
    echo '<label><input type="radio" name="tablesorter_admin" value="0" '.$no.' >Disabled</label> &nbsp; &nbsp; ';
    echo '<label><input type="radio" name="tablesorter_admin" value="1" '.$yes.' >Enabled</label>  &nbsp; &nbsp; <input type="submit" class="button-primary" />';
    echo "</p></form>";
    echo '<br /><hr>';
    echo tablesorter_docs();
}

function tablesorter_menu(){
    add_options_page('Table Sorter', 'Table Sorter', 'manage_options', 'table-sorter', 'tablesorter_settings');
}
add_action('admin_menu', 'tablesorter_menu');

function tablesorter_row_meta( $links, $file ) {
	if ( strpos( $file, 'table-sorter.php' ) !== false ) {
		$new_links = array('<a href="http://wpreloaded.com/plugins/table-sorter/how-to/" target="_blank">Docs</a>');
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'tablesorter_row_meta', 10, 2 );

function tablesorter_add_action_links ( $links ) {
	$mylinks = array('<a href="' . admin_url( 'tools.php?page=table-sorter' ) . '">Docs</a>');
	return array_merge( $links, $mylinks );
}
add_filter( 'plugin_action_links_table-sorter/table-sorter.php', 'tablesorter_add_action_links' );

function tablesorter_docs(){
    ob_start();
    ?>
    <style>
	ul{list-style:inherit; padding-left:20px} .label{font-size: 15px; font-weight: 600; line-height: 1.3}
    </style>
    <h2 class="title">How To</h2>
    <b>Table Sorter</b> turns standard HTML table with THEAD and TBODY tags into a sortable table without page refresh. It works on every table, whether it is coded in the wordpress template(theme) file or generated from the wordpress editor. This plugin is very handy for theme developers. It has many useful features including :
    <ul>
        <li><b>Multi-column sorting :</b> add "tablesorter" class to the required table</li>
        <li><b>Disable sorting from particular column :</b> add "sortless" class to the required TH column within THEAD tag.</li>
        <li><b>Sort multiple columns simultaneously :</b> by holding down the shift key and clicking a second, third or even fourth column header! </li>
        <li><b>Cross-browser support :</b> IE 6.0+, FF 2+, Safari 2.0+, Opera 9.0+</li>
    </ul>
    <h2 class="title">IMPORTANT</h2>
    <ol>
        <li><b>THEAD</b> and <b>TBODY</b> tags are compulsory in the desired table, otherwise this plugin will not work.</li>
        <li>Add <b><u>tablesorter</u></b> class in the desired <b>TABLE</b> tag.</li>
        <li>For column heading, use <b>TH</b> tag within <b>THEAD</b> tag.</li>
        <li>If you want to exclude a particular column, add <b><u>sortless</u></b> class to that <b>TH</b> tag.</li>
    </ol>
    <h2 class="title">Initial Sorting</h2>
    <p>You can add initial sorting by adding instructions in the CLASS attribute of the HTML table in the format: {sortlist: [[columnIndex, sortDirection], … ]} where columnIndex is a zero-based index for your columns left-to-right and sortDirection is 0 for Ascending and 1 for Descending. A valid argument that sorts ascending first by column 1 and then column 2 looks like: {sortlist: [[0,0],[1,0]]}</p>
    <p><strong>Example : </strong>&lt;table id="myTable" class="tablesorter {sortlist: [[2,0]]}"&gt;</p>
    <h2 class="title">Sort by date</h2>
    <p>To make your date columns sortable, add class <code>dateFormat-dd/mm/yyyy</code> in your Date column head. You can change date format according to your own need.</p>
    <p><b>Example: </b><code>&lt;th class="dateFormat-dd/mm/yyyy"&gt;Date&lt;/th&gt;</code></p>
    <p>All Done. Have fun!</p>
<!--Generating Table HTML to show-->
    <p><strong>For complete documentation and demo, please visit <a href="http://wpreloaded.com/plugins/table-sorter/how-to/" target="_blank">WP Table Sorter</a>  plugin support page. Happy coding!</strong></p>
    <?php
    return ob_get_clean();
}