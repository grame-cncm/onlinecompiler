<?php

/*! \file form.php
    \brief This file contains some functions used by the Faust Server.
    \author Romain Michon - Damien Cramet

    These functions manage the different menus.

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

if (!defined("__form__")) {
  define("__form__", 1);
  if (session_id()=="") session_start();

/*! \fn make_menu ($tag, $name, $select, $method, $list) 
*   \brief This function creates the architecture's menu.
    \param $tag The indicator (__tag__) in html code where the menu will be put.
    \param $name The menu's name.
    \param $select The selected value in the menu.
    \param $method The methods definde for the menu (onChange, ...).
    \param $list The list displayed in the menu.
*   \return The complete menu.
*/

  function make_menu ($tag, $name, $select, $method, $list, $type, $disab) {
    if($disab == "disabled") $disabled = "disabled=\"disabled\"";
    if($type == "opt") $assoc[$tag] = "<SELECT ID=\"$name\" NAME=\"$name\" $disabled $method >".make_menu_list_opt($list, $select)."</SELECT>";
    if($type == "cat") $assoc[$tag] = "<SELECT ID=\"$name\" NAME=\"$name\" $disabled $method >".make_menu_list_cat($list, $select)."</SELECT>";
    return $assoc ;
  }

  /*
    disableElements: This function disabled the elements specified in $listAllowed in $list
   */

  function disableElements($list,$listAllowed){
    $assoc = "";
    $i = 0;
    while($list[$i] != "#"){
      $j = 0;
      while(($listAllowed[$j] != "") && ($listAllowed[$j] != $list[$i])){
	$j = $j + 1;
      }
      if($listAllowed[$j] == "") $assoc = $assoc."document.getElementById(\"ARCH$list[$i]\").disabled=true;";
      else $assoc = $assoc."document.getElementById(\"ARCH$list[$i]\").disabled=false;";
      $i = $i + 1;
    }
    return $assoc;
  }

/*! \fn make_menu_list($list,$select)
/*  \brief This function creates the architecture menu's list.
    \param $list The list of options
    \param $select The selected value
    \return The menu's list.
*/

  function make_menu_list_opt($list,$select){
    $i=0;
    $menu="";
    while ($list[$i] != "#"){
      if ($list[$i] != ""){
	if ($list[$i] == $select) $sel = " SELECTED";
	else $sel = "";
	$menu = $menu."<OPTION id=\"ARCH$list[$i]\" VALUE=\"$list[$i]\"$sel>$list[$i]\n";
      }
      $i = $i+1 ;
    }
    return $menu ;
  }

/*! \fn make_menu_list($list,$select)
/*  \brief This function creates the menu's list.
    \param $list The list of options
    \param $select The selected value
    \return The menu's list.
*/

  function make_menu_list_cat($list,$select){
    $i=0;
    $menu="";
    while ($list[$i] != ""){
      if ($list[$i] != ""){
	if ($list[$i] == $select) $sel = " SELECTED";
	else $sel = "";
	$menu = $menu."<OPTION id=\"elem$list[$i]\" VALUE=\"$list[$i]\"$sel>$list[$i]\n";
      }
      $i = $i+1 ;
    }
    return $menu ;
  }
}
?>
