<?php
//------------------------------------------------
//--AUTHOR: Smilingdog Productions
//--Creation date: 8/1/13
//--Copyright © 2006-2014 Smilingdog Productions
//---------------------------------------
// $php/util/echo_one.php
//----------------
	$short= false;														//--$short (circuit) "Echos" constant
//--
defined('Echos') || define('Echos', $bool= false);				//--Set echo_one() on/off ~ (true/false)
//--
defined('V') || define('V', DIRECTORY_SEPARATOR);				//--Set os dependent dir. separator
//--------
//--Format ~ (var. precedence, any combination)
//--
//--Ex. call: echo_one(__FILE__,__FUNCTION__,__LINE__, compact('var1', 'var2', ''), 'const1' 'const2');
//--
//----------------
function bN_($_file_){
//----------------
//--Basename str
//--
	$script= $bnStr= basename($_file_);
//--
if (strpos($_file_, V) !== false){
	$strPos= (strrpos($_file_, V) +1);
	$script= substr($_file_, $strPos);
	}
if ($script === $bnStr){
	$os= (!strpos($bnStr, '.php')) ? 5 : 4;
	$strPos= ($os == 5) ? (strpos($bnStr, '.html') +$os) : (strpos($bnStr, '.php') +$os);
	$bnStr= substr($bnStr, 0, $strPos);
	}
//--
return $bnStr;
//--
}//end of function bN_
//----------------
function typeCheck($typeVar){
//----------------
global $retType;
//--
	$varType= gettype($typeVar);
//--
switch ($varType){														//--Separate & index headers
	case 'array'	: 
	case 'boolean'	: $retType= $varType; break;
	case 'double'	: 
	case 'float'	: $retType= 'double'; break;
	case 'integer'	: $retType= $varType; break;
	case 'NULL'	: $retType= 'null'; break;
	case 'numeric'	: 
	case 'object'	: 
	case 'resource'	: 
	case 'scalar'	: 
	case 'string'	: $retType= $varType; break;
	default 	: $retType= 'unknown type';
	}//end of switch
//--
return $retType;
//--
}//end of function typeCheck
//----------------
function echo_one(){
//----------------
global $short;
//--
if (!$short && !Echos) return;							//--Set echo_one() on/off ~ (true/false)
//--
	$mCs= 3;															//--magic constants index
//--
	$spc= '';															//--space char holder
	$fnStr= '';															//--file name holder
	$funcStr= '';														//--func name holder
	$lnNum= 0;															//--line number holder
//--
	$echo_str= '';														//--echoing string
//--------
//--All cases
//--
	$arg_list= func_get_args();											//--var. lgth. arg. list ~ array
//--
	$funcArgs= func_num_args();											//--this func's arg's count
//--
if (!empty($arg_list[0])){
//--
for ($aL= 0; $aL < $funcArgs; $aL++){
//--------
//--File ~ func @ line:
//--
	if ($aL <= $mCs){													//--string arg w/ dir separator
		switch (typeCheck($arg_list[$aL])){
			case "array" : 
				if ($funcArgs >= 1 && $aL <= 4){
					$mCs= $aL;											//--$mCs: magic constants arg's
					$compacts= count($arg_list[$mCs]);		//--compact func's arg's count
					}
				break;
			case "integer" : 
				$lnNum= $arg_list[$aL];									//--line number arg
				break;
			case "string" : 
				if (strpos($arg_list[$aL], V) !== false){		//--string arg w/ dir separator
					$frag= explode(V, $arg_list[$aL]);		//--frag: file path parts array
					$frags= count($frag);
//--
					$fnStr= (bN_($frag[($frags -1)]).' ~ ');	//--call file name str func bN()
					}
				else
					$funcStr= $arg_list[$aL];							//--call file name str func bN()
				break;
			default : 
			}//end of switch
		}//end of if $aL <= $mCs
	}//end of for funcArgs loop
//--
	}//end of if !empty($arg_list[0])
else
	return;
//--------
//--File name
//--
if (!empty($fnStr)){
	$echo_str.= 'In '.$fnStr;
	$spc= ((strrpos($echo_str, '~') +2) == strlen($echo_str)) ? '' : ' ';		//--space/no space
	$echo_str.= (!empty($lnNum) || !empty($funcStr)) ? $spc : '
';
	}
//--------
//--Func name
//--
if (!empty($funcStr)){
	$echo_str.= 'function '.$funcStr;
	$echo_str.= ($compacts == 1)  ? ' @ ' : ((!empty($lnNum)) ? ' @ ' : ': 
');
	}
//--------
//--Line number
//--
if (!empty($lnNum)){
	$echo_str.= (!empty($spc)) ? '() ln_' : 'ln_'.$spc;
	$echo_str.= ($compacts == 1) ? $lnNum.': ' : $lnNum.': 
';
	}
//--------
//--Load all name/values
//--
for ($aL= 0; $aL < $funcArgs; $aL++){							//--top numeric index ~ $key
//--------
//--Compact var values
//--
	if ($aL === $mCs && $funcArgs > $mCs && $compacts > 0){				//--$arg_list[#] (array) ~ length
		foreach ($arg_list[$mCs] as $key=> $value){				//--numeric index ~ $key
//--
			$arg_name[$key]= '$'.$key;
			$arg_value[$key]= $value;
//--------
//--Load echo str
//--
			$echo_str.= $arg_name[$key].'= '.$arg_value[$key].' 
';
			}//end of foreach arg_list loop
		}//end of if $aL === $mCs && $funcArgs > $mCs && $compacts > 0: total magic const arg's
//--------
//--Additional name/values
//--
	if ($aL >= $compacts && $funcArgs > $compacts){					//--$arg_list[#] (array) ~ length
//--------
//--Constant var values
//--
		if (defined($arg_list[$aL])){
			$arg_name[$aL]= $arg_list[$aL];
			$arg_value[$aL]= constant($arg_list[$aL]);
//--------
//--Load echo str
//--
			$echo_str.= $arg_name[$aL].'= '.$arg_value[$aL].' 
';
			}//end of if defined($arg_list[$aL])
		}//end of if $funcArgs > $compacts
//--
	}//end of for all funcArgs loop
//--
	$echo_str.= '
';
//--------
//--Echo the args
//--
	echo $echo_str;
//--
}//end of function echo_one
//----------------
//--
/*
	"To Do List"
---------------------	
1).	Add proc. for array values.
--------------------------------
*/
/*
*/
?>
