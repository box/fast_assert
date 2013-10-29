<?php

function will_throw(Callable $c)
{
	try
	{
		$c();
	}
	catch (Exception $e)
	{
		echo "Exception thrown\n";
	}
}

function will_not_throw(Callable $c)
{
	$c();
	echo "No exception thrown\n";
}
