<?php

/*! \file functions.php
    \brief This file contains some functions used by the Faust Server.
    \author Romain Michon - Damien Cramet

    These functions manage the input-outputs and the treatments of strings.

    Copyright (C) 2003-2011 GRAME, Centre National de Creation Musicale
    ---------------------------------------------------------------------
    This file is free software; you can redistribute it
    and/or modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 3 of
    the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; If not, see <http://www.gnu.org/licenses/>.

    EXCEPTION : As a special exception, you may create a larger work
    that contains this FAUST architecture section and distribute
    that work under terms of your choice, so long as this FAUST
    architecture section is not modified.
*/

if (!defined("__functions__"))
{
	define("__functions__", 1);
	if (session_id()=="") session_start();

/*! \fn erreur($msg)
*   \brief This function allows to display the page erreur.html with an error message
*/
        function erreur($msg)
        {
		$html = read_file("erreur.html");
                $assoc[__msg__] = $msg;
                $html = fill_template($html, $assoc);
                print $html;
		exit();
	}

/*! \fn read_firstline($file)
*   \brief This function allows to read the first line of a file and to put it in a string.
    \param $file The name of the file to read.
    \return The content of the file in a string.
*/

	function read_firstline ($file)
	{
		$fd=fopen($file, "r");
		if (!$fd)
			erreur ("erreur ouverture de $file.");
		$content=fgets($fd);
		if (!$content)
			erreur ("erreur lecture de $file.");
		fclose ($fd);
		return $content;
	}

/*! \fn read_file($file)
*   \brief This function allows to read the content of a file and to put it in a string.
    \param $file The name of the file to read.
    \return The content of the file in a string.
*/

	function read_file ($file)
	{
		$fd=fopen($file, "r");
		if (!$fd)
			erreur ("erreur ouverture de $file.");
		$content=fread($fd, filesize($file));
		if (!$content)
			erreur ("erreur lecture de $file.");
		fclose ($fd);
		return $content;
	}

/*! \fn fill_template ($content, $assoc)
*   \brief This function allows to fill an html section with the array $assoc
    \param $content The html section to fill
    \param $assoc The array containing the values to put in the section
    \return The html section filled

    Each template (__template) of the html section will be filled by
    a value of the array $assoc.
*/

	function fill_template ($content, $assoc)
	{
		if ($content=="") return "";
		while (list($key, $val) = each($assoc)) {
			if ($key[0] == '_')
				$content=str_replace ($key, $val, $content);
		}
		return $content;
	}

/*! \fn get_header($content, $tag)
*   \brief This function allows to extract a part of an html page, from the top to an indicator.
    \param $content The html code.
    \param $tag The indicator which marks the end of the part to extract.
    \return The extracted part of the html code.
*/

	function get_header ($content, $tag)
        {
		if (preg_match ("/^.*<!-- *$tag *-->/s", $content, $res))
			return $res[0];
		erreur("en_tete \"$tag\" non trouve�");

	}

/*! \fn get_footer($content, $tag)
*   \brief This function allows to extract a part of an html page, from an indicator to the bottom.
    \param $content The html code.
    \param $tag The indicator which marks the begin of the part to extract.
    \return The extracted part of the html code.
*/

	function get_footer ($content, $tag)
        {
		if (preg_match ("/<!-- *$tag *-->.*$/s", $content, $res))
			return $res[0];
		erreur(" pied_de_page \"$tag\" non trouve�");

	}

/*! \fn get_section($content, $section)
*   \brief This function allows to extract a part of an html page, between two indicators.
    \param $content The html code.
    \param $section The name of the section to extract.
    \return The extracted part of the html code.

    Each section of the html code is delimited by two indicators
    <\!-- section --> <\!--/section-->
*/

	function get_section ($content, $section)
        {
		if (preg_match ("/<!-- *$section *-->.*<!-- *\/$section *-->/s", $content, $res))
			return $res[0];
		erreur("section \"$section\" non trouvee");

	}

/*! \fn put_in_tmp_file($code, $template, $suffixe)
*   \brief This function allows to put some code in a temporary file.
    \param $code The source code.
    \param $template A template to generate the temporary file name.
    \param $suffixe The extension of the temporary file.
    \return The name of the temporary file.
*/

        function put_in_tmp_file($code, $template, $suffixe)
        {
          $tmp1 = tempnam( $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp", $template );
          if ($tmp1)
          {
            $tmp = $tmp1.$suffixe;
            rename ($tmp1, $tmp);
            $fdin = fopen($tmp, "w");
            fputs( $fdin, $code ) ;
            fclose( $fdin );
            return $tmp;
          }
          return $tmp1;
        }

/*! \fn Remove_CR($text)
*   \brief This function allows to delete the Carriage Return (not supported by Faust).
    \param $chaine The string to treat.
    \return The treated string.
*/

	function Remove_CR($text)
	{
		$longueur=strlen($text);
		$prec = 0 ;
		$newchaine="";
		for($i=0;$i<$longueur;$i++)
		{
                  $caractere=substr($text,$i,1);
                  if (ord($caractere) == 13 && $prec = 10) null ;

                  else
                  {
                    $prec = ord($caractere);
                    $newchaine = $newchaine.$caractere ;
                  }
		}
		return $newchaine;
	}

/*! \fn extract_code($page)
*   \brief This function allows to extract the C++ highlighted code in the page
    \brief generated by highlight.
    \param $page The page generated by highlight.
    \return The C++ highlighted code.
*/

        function extract_code($page)
        //extrait le code highlighte de la pge html generee par highlight
        {
          list($other,$code) = split('<body class="hl">', $page);
          list($code,$other) = split('</body>', $code);
          return $code;
        }

/*! \fn extract_make_cmd($page)
*   \brief This function allows to extract a compilation command from the Makefile
    \brief and to fill it with the good arguments.
    \param $enrob The entry to extract.
    \return The compilation command.
*/

        function extract_make_cmd($enrob)
        {
            $makefile = read_file("Makefile");
            list($other,$cmd) = split($enrob, $makefile);
            list($cmd,$cmd2,$other) = split(chr(10),$cmd);
            $cmd2 = str_replace("$(SRC)", $_SESSION['appli_name'].".cpp", $cmd2);
            $cmd2 = str_replace("$(DEST)", $_SESSION['appli_name'], $cmd2);
            return "all : ".chr(10).chr(9).$cmd2 ;
        }

/*! \fn extract_options($options)
*   \brief This function allows to extract the list of options from the -h Faust option.
    \param $options The -h option result.
    \return The list of options.
*/

	function extract_options($options)
	{
		$i=0;
		$opt="";
		$n = count ($options);
		while($options[$i]!="") {
		// the first empty line is expected to separate the 'usage' part and the options enumeration
			 $i=$i+1;
			 if (($i > 10) || ($i > $n)) return "Failed to extract faust options<br />";
		}
		while ($i < $n) {
			
			if ($options[$i] == "Example:") break;						// skip the examples
			$tmp = preg_replace ('/</', '&lt;' ,$options[$i]);			// replace '<' to avoid html syntax
			$tmp = preg_replace ('/^([A-Za-a]..*)$/', '<h3>$1</h3> ', $tmp);	// sections headers
			$tmp = preg_replace ('/^--*$/', '<hr> <ul> ', $tmp);		// separation line and indent (<ul>)
			$tmp = preg_replace ('/^  *(-..*)$/', '$1<br> ', $tmp);		// option: add a <br>
			$tmp = preg_replace ('/^[ 	]*$/', '</ul> ', $tmp);			// empty lines: unindent (</ul>)
			$opt = $opt . " " . $tmp;
			$i = $i + 1;
		} 
		return $opt;
	}
        
//         function old_extract_options($options)
//         {
//           $i=0;
//           $opt="";
//           while($options[$i]!="---------") $i=$i+1;
//           $i=$i+1;
//           while($options[$i]!="")
//           {
//             if (substr_count($options[$i], "-h") == 0 && substr_count($options[$i], "-o") == 0
//                 && substr_count($options[$i], "-a") == 0 && substr_count($options[$i], "-svg")== 0
//                 && substr_count($options[$i], "-v") == 0)
//               $opt = $opt.str_replace("<n>","",$options[$i]."<br>");
//             $i=$i+1;
//           }
//           return $opt;
//         }

	function cutEnrobagemenu($stringToCut){
	  $cutPosition32 = strpos($stringToCut,"3");
	  $cutPosition64 = strpos($stringToCut,"6");
	  if($cutPosition32 > 0 || $cutPosition64 > 0){
	    $cutPosition = ($cutPosition32.$cutPosition64)-1;
	    $cutedString = substr($stringToCut,0,$cutPosition);
	    return $cutedString;
	  }
	  else{
	    return $stringToCut;
	  }
	}

/*! \fn traiter_chaine($chaine)
*   \brief This function allows to delete the chars (\) added by php.
    \param $chaine The string to treat.
    \return The treated string.
*/

## Cette fonction pose probleme pour les lambda de faust \(x).(x*x) qu'elle detruit
## En outre je ne comprend pas trop son utilité ??? Y.O.
        function traiter_chaine( $chaine )
        {
          $s1 = str_replace( "\\\"", "\"", $chaine);
	  $s2 = str_replace( "\\\\", "\\", $s1);
	  $s3 = str_replace( "\\'", "'", $s2);
	  return $s3;
	  ## return $chaine;
        }

}

?>
