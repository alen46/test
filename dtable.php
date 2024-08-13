<?php
 

$table = 'employees';
$primaryKey = 'emp_no';
$columns = array(
    array( 'db' => 'emp_no', 'dt' => 0 ),
    array( 'db' => 'name',  'dt' => 1 ),
    array( 'db' => 'hire_date',     'dt' => 3 ),
    array( 'db' => 'email',     'dt' => 2 ),
);
$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db'   => 'test',
    'host' => 'localhost'
);
 
require( 'res/ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);