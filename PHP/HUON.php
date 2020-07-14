<?php
//--------------------------------------------------------------------//
//		HUON.php: David Spector, 6/20/20, version 1.00
//		See HUON.js for initial comments
//
//		Copyright © 2020 Springtime Software
//		The terms of the license and copyright for this software are as
//		specified in the included file named LICENSE.
//--------------------------------------------------------------------//

// Global HUON version and default option values
define("VERSION","1.005");
define("DISABLETRAILINGCOMMAS",0);
define("MAX_NUMBER_CHARS_LEN",8);

//define("ENABLE_STACK_ON_ERROR",0);

class HUON
	{
	static public
	$ver=VERSION, // HUON version
	$DisableTrailingCommas=DISABLETRAILINGCOMMAS,
	$MaxNumberCharsLen=MAX_NUMBER_CHARS_LEN;
	static private
	// Global private vars for HUON, starting with 'g'
	$gStr,$gSite,	// Saved obj() args
	$gws=" \t\r\n",	// Whitespace chars
	$goff,			// Cur offset in gStr
	$gstart,		// Start of cur string for backtracking
	$gres;			// Function result

	static public function obj($Str,$Site='',&$ErrOutStr)
		{
		try {return HUON::obj3($Str,$Site);}
		catch(Exception $error) {HUON::HandleErr($error,$ErrOutStr);}
		} // obj2

	static private function HandleErr($error,&$ErrOutStr)
		{
		if ($ErrOutStr===null)
			Exit($error->getMessage());
		else
			$ErrOutStr=SafeHTML($error->getMessage());//qq where is SafeHTML?
		} // HandleErr

	static private function obj3($Str,$Site)
		{
		HUON::$gStr=$Str; HUON::$gSite=$Site;
		HUON::$goff=0;
		$st=gettype(HUON::$gStr);
		if ($st != 'string')
			HUON::Err('First argument to HUON::obj was of type '.$st.', not string');
		HUON::removeComments();
		$val=HUON::value();
		HUON::skipSpace();
		if (HUON::$goff>strlen(HUON::$gStr))
			{
			HUON::Err('Internal: offset overrun at end of "'.HUON::$gStr.'"');
			}
		$remain=HUON::remaining();
		if ($remain)
			HUON::SyntaxErr((string)strlen($remain).' unrecognized character(s) found after a valid HUON substring');
		return $val;
		} // obj3

	// Comment out "replace" lines here to
	// remove support for either kind of comments
	static private function removeComments()
		{
		//HUON::$gStr='';

		//		"//"
		HUON::$gStr=preg_replace('@//.*$@','',HUON::$gStr);

		//		"/* ... */"
		HUON::$gStr=preg_replace('@/\*.*?\*/@s','',HUON::$gStr);

		//D(HUON::$gStr);
		} // removeComments

	static private function value()
		{
		if (HUON::isAtNumber())
			return HUON::$gres;
		if (HUON::isAtString())
			return HUON::$gres;
		if (HUON::isAtArrayOrObj(true)) // Array
			return HUON::$gres;
		if (HUON::isAtArrayOrObj(false)) // Object
			return HUON::$gres;
		return HUON::SyntaxErr("A value was not found");
		} // value

	static private function isAtArrayOrObj($type/*false:Array,true:Object*/)
		{
		$left=$type?'{':'[';
		$right=$type?'}':']';
		$kind=$type?'An object property':'An array element';
		if (!HUON::isAt($left))
			return false;
		if ($type)
			$result=(object)[]; // {} Accumulated value
		else
			$result=[];
		HUON::$gres=$result;
		if (HUON::isAt($right))
			return true; // Seen: [] Note: or {}
		// Seen: [
		for (;;) // Loop for each array or object element
			{
			// Append next array element or object property
			// Seen: [ | [value, | [value,value, | ...
			if ($type)
				{
				if (!HUON::isAtString())
					return HUON::SyntaxErr("A property key was not found");
				$key=HUON::$gres;
				if (!HUON::isAt(":"))
					return HUON::SyntaxErr("A property key/value separator ':' was not found");
				$v=HUON::value();
				$result->$key=$v;
				} // $type=object
			else
				array_push($result,HUON::value()); // $type=array

			if (HUON::$DisableTrailingCommas)
				{
				// Seen: [value | [value,value | ...
				if (HUON::isAt(","))
					continue;
				} // DisableTrailingCommas

			else // Allow trailing commas
				{
				// Seen: [value | [value,value | ...
				if (HUON::isAt(","))
					{
					// Seen: [value, | [value,value, | ...
					if (!HUON::isAt($right))
						continue; // Append next value
					// Seen: [value,] | [value,value,] | ...
					HUON::$gres=$result;
					return true;
					} // isAt comma
				} // Allow trailing commas

			// Seen: [value | [value,value | ...
			if (!HUON::isAt($right))
				return HUON::SyntaxErr($kind." was not terminated by ',' or '".$right."'");
			// Seen: [value] | [value,value] | ...
			HUON::$gres=$result;
			return true;
			} // Loop for each array or object element
		} // isAtArrayOrObj

	// See if looking at a specific string,
	// skipping any whitespace first, and skip the string
	static private function isAt($str)
		{
		HUON::skipSpace();
		//HUON::$gstart=HUON::$goff;
		if (substr(HUON::$gStr,HUON::$goff,strlen($str))!=$str)
			return false;
		HUON::$goff+=strlen($str);
		return true;
		} // isAt

/*
	// See if looking at a specific unquoted string followed by a nonstring char
	static private function isAtSpecificStr($str)
		{
		$gstart=HUON::$goff;
		if (HUON::isAtPlainString() && HUON::$gres===$str)
			return true;
		HUON::$goff=$gstart;	// Backtrack on failure to match
		return false;
		} // isAtSpecificStr
*/

	// Skip any whitespace where allowed
	static private function skipSpace()
		{
		$t=HUON::remaining();
		$tr=ltrim($t,HUON::$gws);
		HUON::$goff+=(strlen($t)-strlen($tr));
		} // skipSpace

	// Return prefix (leading) string, if any
	static private function leading()
		{
		return substr(HUON::$gStr,0,HUON::$goff);
		} // leading

	// Return suffix (remaining) string, if any
	static private function remaining()
		{
		return substr(HUON::$gStr,HUON::$goff);
		} // remaining

	// Find a regexp pattern, with optional
	// paren subpat (first is 1)
	static private function isAtPattern($pattern,$patNr=0,&$lenOut=null)
		{
		HUON::skipSpace();
		//$pattern2=preg_quote($pattern);
		//D($pattern2);
		$res=preg_match("@$pattern@",HUON::remaining(),$arr);
		if ($res===false)
			Err("ERR H2");
		if (!$res)
			return false;
		if ($lenOut!==null)
			$lenOut=strlen($arr[0]); // Optional: return entire pattern length
		HUON::$gres=$arr[$patNr]; // Result is just the paren subpat
		HUON::$goff+=strlen($arr[0]); // Skip the entire pattern
		return true;
		} // isAtPattern

	// See if looking at number literal
	static private function isAtNumber()
		{
		$lenOut=0;
		if (!HUON::isAtPattern('^[+-]?[0-9]+',0,$lenOut))
			return false;
		if ($lenOut > HUON::$MaxNumberCharsLen)
			HUON::SyntaxErr('A number contained more than '.HUON::$MaxNumberCharsLen.' characters');
		HUON::$gres=intval(HUON::$gres,10);
		return true;
		} // isAtNumber

	// See if looking at an unquoted string:
	// string not including []{},: and whitespace
	static private function isAtPlainString()
		{
		if (!HUON::isAtPattern('^[^[\]{},: \n\t]+'))
		//if (!HUON::isAtPattern(/^[A-Za-z$_][A-Za-z$_0-9-]* /))
			return false;
		return true;
		} // isAtPlainString

	// See if looking at a string, whether quoted or not
	static private function isAtString()
		{
		// Quoted strings have priority
		if (HUON::isAtPattern('^"(.*?)"',1))
			return true;
		if (HUON::isAtPattern("^'(.*?)'",1))
			return true;

		// Unquoted strings and literals are also supported
		if (HUON::isAtPlainString())
			{
			HUON::handleLiteral();
			return true;
			}
		return false;
		} // isAtString

	static private function handleLiteral()
		{
		if (HUON::$gres=='true')
			HUON::$gres=true;
		else if (HUON::$gres=='t')
			HUON::$gres=true;
		else if (HUON::$gres=='false')
			HUON::$gres=false;
		else if (HUON::$gres=='f')
			HUON::$gres=false;
		else if (HUON::$gres=='null')
			HUON::$gres=null;
		//else if (HUON::$gres=='HUON')
		//	HUON::SyntaxErr("HUON symbol");
		} // handleLiteral

	// Show current parsed string with context indication
	// Also shows internal overrun error for debugging
	static private function context()
		{
		$over=(HUON::$goff>strlen(HUON::$gStr))?'(***overrun)':'';
		// Arrow candidates:
		// heavy vertical bar: %e2%9d%9a; right triangle: %E2%96%B6; Bright Button: \xf0\x9f\x94\x86;
		// dim button: \xf0\x9f\x94\x85
		$Arrow=hex2bin("f09f9485");
		return '"'.HUON::leading().$Arrow.HUON::remaining().'"'.$over;
		} // context

	static private function SyntaxErr($msg)
		{
		if (gettype(HUON::$gStr) != 'string')
			HUON::Err($msg);
		$msg2='Syntax error: '.$msg.' at '.HUON::context();
		HUON::Err($msg2);
		} // SyntaxErr

	static private function Err($msg) // Raise an exception and exit
		{
		$msg2='*** '.$msg;
		if (HUON::$gSite)
			$msg2.=', for HUON string with label: '.HUON::$gSite;
		if (ENABLE_STACK_ON_ERROR)
			HUON::OutputStack();
		//$msg2=N.HUON::GetStackStr();
		//console.log($msg2);
		//alert($msg2.n/*.new Error().stack* /);
		throw new Exception($msg2);
		exit('ERR H1');
		} // Err

	static private function OutputStack()
		{
		?><strong>Calling stack:</strong><pre><?
		var_dump(array_reverse(debug_backtrace()));
		?></pre><?
		} // OutputStack

	} // class HUON
?>
