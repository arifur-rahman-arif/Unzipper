<?php

if (isset($_POST['zip_file'])) {

	$filename = $_POST['zip_file'];
	$source = $_POST['zip_file'];
	$type = 'zip';

	$name = explode(".", $filename);
	$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
	foreach ($accepted_types as $mime_type) {
		if ($mime_type == $type) {
			$okay = true;
			break;
		}
	}

	/* PHP current path */
	$path = dirname(__FILE__) . '/';  // absolute path to the directory where zipper.php is in
	$filenoext = basename($filename, '.zip');   // absolute path to the directory where zipper.php is in (lowercase)
	$filenoext = basename($filenoext, '.ZIP');  // absolute path to the directory where zipper.php is in (when uppercase)
	$targetdir = $path . $filenoext; // target directory
	$targetzip = $path . $filename; // target zip file

	/* create directory if not exists', otherwise overwrite */
	/* target directory is same as filename without extension */
	if (is_dir($targetdir))
		rmdir_recursive($targetdir);

	mkdir($targetdir, 0777);

	/* here it is really happening */
	$zip = new ZipArchive();
	$x = $zip->open($targetzip);  // open the zip file to extract
	if ($x === true) {
		try {
			$zip->extractTo($targetdir); // place in the directory with same name
		} catch (Exception $e) {
			echo 'Something went wrong here, maybe nothing all is unzipped';
		}
		$zip->close();
		// unlink($targetzip);
		echo "Your .zip file was successfully unzipped.";
	}
}

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
echo '<title>Unzip a zip file already on the web server</title>';
echo '</head>';
echo '<body>';
echo '<h1>Unzip a file already on the server</h1>';
echo '<br/>Use your favorite ftp program to upload large zip files to the server.<br/>';
echo 'Put it in the same directory as this unzip.php file, it is recommended to make a "zipper" directory.<br/>';
echo 'After uploading the file you select the file here and click the unzip button. It is unzipped in no time<br/>';

if ($message)
	echo '<p>$message</p>';

echo '<form method="post" action="">';
echo '<select name="zip_file">';

$dir = '.';
$dh = opendir($dir);
while (false !== ($fn = readdir($dh))) {
	$ext = substr($fn, strrpos($fn, '.') + 1);
	if (in_array($ext, array("zip", "ZIP"))) {
		echo '<option value="' . $fn . '">' . $fn . '</option>';
	}
}
echo '</select>';
echo '<br /><br />';
echo '<input type="submit" name="submit" value="Unzip" />';
echo '</form>';
echo '</body>';
echo '</html>';

function rmdir_recursive($dir)
{
	foreach (scandir($dir) as $file) {
		if ('.' === $file || '..' === $file)
			continue;
		if (is_dir("$dir/$file"))
			rmdir_recursive("$dir/$file");
		else
			unlink("$dir/$file");
	}
	rmdir($dir);
}
