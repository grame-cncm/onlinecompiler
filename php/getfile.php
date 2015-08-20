<?php

/*! \file getfile.php
    \brief This file contains the functions getfile and getpackage
    \author Damien Cramet
*/

if (!defined("__getfile__"))
{
	define("__getfile__", 1);

/*! \fn getfile ($fichier, $enrob)
 *  \brief A member function.
 *  \param $fichier the name of the downloaded file
 *  \param $enrob the architecture of the downloaded file

 This function build the path of the file that will be downloaded.
 Then it places the navigator's headers to force the download, and
 opens the file.

 */

 	// Fonction permettant le telechargement d'un fichier
	function OLDgetfile($fichier, $enrob)
	{
		if (session_id()=="") session_start();

		$nomFichier = $fichier;
                //telechargement d'un exemple
		if ($enrob!="") $fichier = "exemples/exec/".$enrob."/".$fichier;
                //telechargement d'un fichier compile par l'utilisateur
		else $fichier = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/".$_SESSION['appli_name']."/".$fichier;
		$tailleFichier = filesize($fichier);
		//Configuration du navigateur
		header('Content-Type: application/x-executable-file');
		#header('Content-Type: application/octet-stream');
		header("Content-Length: $tailleFichier");
		header("Content-Disposition: attachment; filename=\"$nomFichier\"");
		readfile($fichier);
	}

	function getfile($fname, $enrob)
	{
		if (session_id()=="") session_start();

		//possible filenames depending of the architecture
		$src1 = "exemples/exec/".$enrob."/".$fname;
		$src2 = "exemples/exec/".$enrob."/".$fname.".so";
		$src3 = "exemples/exec/".$enrob."/".$fname."-bin.tgz";

		// try to figure out which executable it is
		if (file_exists($src1)) {
			$srcname = $src1;
			$dstname = $fname;
		} elseif (file_exists($src2)) {
			$srcname = $src2;
			$dstname = $fname.".so";
		} elseif (file_exists($src3)) {
			$srcname = $src3;
			$dstname = $fname."-bin.tgz";
		} else {
			$srcname = 'errorinsrcname';
			$dstname = "error"."__".$name."__".$enrob;
		}
               	$size = filesize($srcname);

		// download the file
		#header('Content-Type: application/x-executable-file');
		header('Content-Type: application/octet-stream');
		header("Content-Length: $size");
		header("Content-Disposition: attachment; filename=\"$dstname\"");
		readfile($srcname);
	}

/*! \fn getpackage ()
 *  \brief A member function.

 This function build the path of the file that will be downloaded.
 Then it places the navigator's headers to force the download, and
 opens the file.

 */

	function getpackage()
	{
		if (session_id()=="") session_start();

		$filename 	= $_SESSION['appli_name']."-pkg.zip";
		$workdirname	= $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/workdir/";
		$fullname 	= $workdirname.$filename;
		$size 		= filesize($fullname);

		header('Content-Type: application/octet-stream');
		header("Content-Length: $size");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		readfile($fullname);
	}

/*! \fn getInWorkdir ()
 *  \brief A member function.

 This function build the path of the file that will be downloaded.
 Then it places the navigator's headers to force the download, and
 opens the file.

 */

	function getInWorkdir($filename)
	{
		if (session_id()=="") session_start();

		$workdirname	= $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/workdir/";
		$fullname 	= $workdirname.$filename;
		$size 		= filesize($fullname);

		header('Content-Type: application/octet-stream');
		header("Content-Length: $size");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		readfile($fullname);
	}
}
/*
This function build the path of the math doc. Then it places the navigator's
headers to force the download, and opens the file.
 */

function getDocPdf()
{
		if (session_id()=="") session_start();

		$appName = $_SESSION['appli_name'];
		$filename 	= $appName.".pdf";
		$workdirname	= $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/workdir/";
		$fullname 	= $workdirname.$appName."-mdoc/pdf/".$filename;
		$size 		= filesize($fullname);

		header('Content-Description: File Transfer');
    	header('Content-Type: application/pdf');
		//header("Content-Length: $size");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		readfile($fullname);
	}
?>
