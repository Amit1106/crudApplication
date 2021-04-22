<?php

//insert.php

include('database_connection.php');

$form_data = json_decode(file_get_contents("php://input"));

$error = '';
$message = '';
$validation_error = '';
$name = '';
$price = '';
$description = '';

if($form_data->action == 'fetch_single_data')
{
	$query = "SELECT * FROM tbl_sample WHERE id='".$form_data->id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['name'] = $row['name'];
		$output['price'] = $row['price'];
		$output['description'] = $row['description'];
	}
}
elseif($form_data->action == "Delete")
{
	$query = "
	DELETE FROM tbl_sample WHERE id='".$form_data->id."'
	";
	$statement = $connect->prepare($query);
	if($statement->execute())
	{
		$output['message'] = 'Data Deleted successfully';
	}
}
else
{
	if(empty($form_data->name))
	{
		$error[] = 'Name is Required';
	}
	else
	{
		$name = $form_data->name;
	}

	if(empty($form_data->price))
	{
		$error[] = 'Price is Required';
	}
	else
	{
		$price = $form_data->price;
	}

	if(empty($form_data->description))
	{
		$error[] = 'Description is Required';
	}
	else
	{
		$description = $form_data->description;
	}

	if(empty($error))
	{
		if($form_data->action == 'Insert')
		{
			$data = array(
				':name'		=>	$name,
				':price'		=>	$price,
				':description'		=>	$description
			);
			$query = "
			INSERT INTO tbl_sample 
				(name, price, description) VALUES 
				(:name, :price, :description)
			";
			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Data Inserted successfully';
			}
		}
		if($form_data->action == 'Edit')
		{
			$data = array(
				':name'	=>	$name,
				':price'	=>	$price,
				':description'	=>	$description,
				':id'			=>	$form_data->id
			);
			$query = "
			UPDATE tbl_sample 
			SET name = :name, price = :price, description = :description 
			WHERE id = :id
			";

			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Data Edited Sucessfully';
			}
		}
	}
	else
	{
		$validation_error = implode(", ", $error);
	}

	$output = array(
		'error'		=>	$validation_error,
		'message'	=>	$message
	);

}



echo json_encode($output);

?>