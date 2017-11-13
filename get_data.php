<?php
/*
 * DataTables server-side processing script
 */

// DB table to use
$table = 'quotes';

// Table's primary key
$primaryKey = 'symbol';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 'symbol', 'dt' => 0 ),
	array( 'db' => 'name', 'dt' => 1 ),
	array( 'db' => 'last', 'dt' => 2 ),
	array( 'db' => 'change', 'dt' => 3 ),
	array( 'db' => 'pctchange', 'dt' => 4 ),
	array( 'db' => 'volume', 'dt' => 5 ),
	array(
		'db' => 'tradetime',
		'dt' => 6,
		'formatter' => function( $d, $row ) {
			if( strtotime(date('Y-m-d', strtotime($d))) == strtotime(date('Y-m-d')))
			{
    		// is today
    		return date('h:m', strtotime($d));
			}
			return date( 'm/d/y', strtotime($d));
		}
	),
);

// SQL server connection information
$sql_details = array(
	'user' => 'root',
	'pass' => '',
	'db'   => 'my_app',
	'host' => 'localhost'
);

require( 'ssp.class.php' );
$where = "active = 1";
echo json_encode(SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $where));
