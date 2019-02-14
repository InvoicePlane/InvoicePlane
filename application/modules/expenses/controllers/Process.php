<?php

# These database settings are set during the initial setup
$DB_HOSTNAME="localhost";
$DB_USERNAME="chris86_invoices";
$DB_PASSWORD="O}S]5qm?L)j(";
$DB_DATABASE="chris86_invoices";
$DB_PORT="3306";

$link = mysqli_connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

if($_POST['action'] == "add")
{


	$payment_method_id = $_POST['payment_method_id'];
	$expense_date = $_POST['expense_date'];
	$tax_rate_id = $_POST['tax_rate_id'];
	$expense_amount = $_POST['expense_amount'];
	$expense_note = $_POST['expense_note'];
	$client_id = $_POST['client_id'];
	
	$query = "Insert into ip_expenses (payment_method_id, expense_date, tax_rate_id,expense_amount,expense_note,client_id) values ('$payment_method_id','$expense_date','$tax_rate_id','$expense_amount','$expense_note','$client_id'";
	$result = mysqli_query($link, $query);
	if(!$result)
	{
		echo "error";
	}
	else
	{
		
	}
	

}
else if($_POST['action'] =="edit")
{
	
}
else
{
}




mysqli_close($link);

header("location: \expenses");

?>