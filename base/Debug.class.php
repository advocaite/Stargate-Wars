<?php
// Base::Debug.class.php
class Debug
{
	function printMsg($className, $function, $message)
	{
		if(DEBUG)
		{
			$sub['{CLASSNAME}'] = $className;
			$sub['{FUNCTIONNAME}'] = $function;
			$sub['{MESSAGE}'] = $message;
			$output = template(TEMPLATES_PATH."debug.tpl", $sub);
			echo $output;
			unset($sub['{CLASSNAME}']);
			unset($sub['{FUNCTIONNAME}']);
			unset($sub['{MESSAGE}']);
		}
	}
}
?>