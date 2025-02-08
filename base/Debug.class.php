<?php
// Base::Debug.class.php
class Debug
{
	function printMsg(string $className, string $function, string $message): void
	{
		if(DEBUG)
		{
			$sub = [
				'{CLASSNAME}' => $className,
				'{FUNCTIONNAME}' => $function,
				'{MESSAGE}' => $message
			];
			$output = template(TEMPLATES_PATH."debug.tpl", $sub);
			echo $output;
		}
	}
}
?>