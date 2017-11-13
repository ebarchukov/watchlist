<?php
/*
 * Actions: add and remove symbol
 */

// SQL server connection information
$sql_details = array(
	'user' => 'root',
	'pass' => '',
	'db'   => 'my_app',
	'host' => 'localhost'
);

// to store errors
$errors = [];

// pass back the data to `api.php`
$form_data = [];

if (!empty($_POST['action']) && $_POST['action'] == 'add')
{
	// validate the form on the server side
	// `symbol` cannot be empty
	if (empty($_POST['symbol']))
	{
		$form_data['success'] = false;
		$form_data['errors']  = 'Symbol name cannot be blank';
		echo json_encode($form_data);
		return;
	}

	try {
		$db = @new PDO(
			"mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
			$sql_details['user'],
			$sql_details['pass'],
			[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
		);
	}
	catch (PDOException $e) {
		$form_data['success'] = false;
		$form_data['errors']  = 'Could not connect to the DB';
		echo json_encode($form_data);
		return;
	}

	// Check if it exists
	$stmt = $db->query("SELECT symbol, active FROM quotes WHERE symbol = '" . $_POST['symbol'] . "'");
	try {
		$stmt->execute();
	}
	catch (PDOException $e) {
		$form_data['success'] = false;
		$form_data['errors']  = 'DB error: Invalid query';
		echo json_encode($form_data);
		return;
	}

	$result = $stmt->fetchAll();
	if (empty($result[0][0]))
	{
		$form_data['success'] = false;
		$form_data['errors']  = 'The given symbol does not exist';
		echo json_encode($form_data);
		return;
	}
	else if ($result[0][1] == 1)
	{
		$form_data['success'] = false;
		$form_data['errors']  = 'This symbol has already been added to the watchlist';
		echo json_encode($form_data);
		return;
	}

	$stmt = $db->prepare("UPDATE quotes SET active = 1 WHERE symbol = '" . $_POST['symbol'] . "'");

	// Execute
	try {
		$stmt->execute();
	}
	catch (PDOException $e) {
		$form_data['success'] = false;
		$form_data['errors']  = 'DB error: Invalid query';
		echo json_encode($form_data);
		return;
	}

	$form_data['success'] = true;
	$form_data['errors']  = 'The symbol is successfully added';
	echo json_encode($form_data);
	return;
}
else if (!empty($_POST['action']) && $_POST['action'] == 'remove')
{
	try {
		$db = @new PDO(
			"mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
			$sql_details['user'],
			$sql_details['pass'],
			array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
		);
	}
	catch (PDOException $e) {
		$form_data['success'] = false;
		$form_data['errors']  = 'Could not connect to the DB';
		echo json_encode($form_data);
		return;
	}

	$stmt = $db->prepare("UPDATE quotes SET active = 0 WHERE symbol = '" . $_POST['symbol'] . "'");

	// Execute
	try {
		$stmt->execute();
	}
	catch (PDOException $e) {
		$form_data['success'] = false;
		$form_data['errors']  = 'DB error: Invalid query';
		echo json_encode($form_data);
		return;
	}

	$form_data['success'] = true;
	$form_data['errors']  = 'The symbol is successfully removed';
	echo json_encode($form_data);
	return;
}
else
{
	$form_data['success'] = false;
	$form_data['errors']  = 'Error: there are no such action';
	echo json_encode($form_data);
	return;
}
