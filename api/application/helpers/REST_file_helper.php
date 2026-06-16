<?php 
 
if (! function_exists('delete_file'))
{
	function delete_file($file = '')
	{
		$file = $_SERVER['DOCUMENT_ROOT'].'/api/files/socialization_event';
		// TODO : check file = is_file, allowed, etc ...
		$file = filter_var($file,FILTER_SANITIZE_STRING);
		if (is_file($file)) {
			return unlink($file);
		}
	}
}

if (! function_exists('make_directory'))
{
	function make_directory($pathint, $mode = 0777 , $recursive = FALSE)
	{
		$pathint = filter_var($pathint,FILTER_SANITIZE_STRING);

		// TODO : check, auth etc
		return mkdir($pathint, $mode, $recursive);
	}
}

if (! function_exists('rename_file'))
{
	function rename_file($oldname, $newname)
	{
		$oldname = filter_var($oldname,FILTER_SANITIZE_STRING);
		$newname = filter_var($newname,FILTER_SANITIZE_STRING);
		// TODO : check, auth etc
		return rename($oldname, $newname);
	}
}

if (! function_exists('copy_file'))
{
	function copy_file($source, $dest)
	{
		$source = filter_var($source,FILTER_SANITIZE_STRING);
		$dest = filter_var($dest,FILTER_SANITIZE_STRING);
		// TODO : check, auth etc
		return copy($source, $dest);
	}
}

if (! function_exists('my_move_uploaded_file'))
{
	function my_move_uploaded_file($filename, $dest)
	{
		$filename = filter_var($filename,FILTER_SANITIZE_STRING);
		$dest = filter_var($dest,FILTER_SANITIZE_STRING);
		// TODO : check, auth etc
		return move_uploaded_file($filename, $dest);
	}
}

if (! function_exists('remove_directory'))
{
	function remove_directory($dirpath)
	{
		$dirpath = filter_var($dirpath,FILTER_SANITIZE_STRING);
		// TODO : check, auth etc
		if (is_dir($dirpath)) {
			rmdir($dirpath);
		}
	}
}