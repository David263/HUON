//--------------------------------------------------------------------//
//		HUON.js: David Spector, 9/2/19, see version below
//
//		HUON(): Converts a minimal JSON variant ("HUman Object
//		Notation") from string to JavaScript value of type Object, a
//		simple Object with no prototype or methods.
//		Warning: this function cannot support standard JSON.
//		Warning: the inverse function (creating a string from an object)
//		might lose information, such as any comments.
//
//		Future: Add optional comment object, containing comments and where
//		they are to be inserted.
//
//		Copyright © 2020 Springtime Software
//		The terms of the license and copyright for this software are as
//		specified in the included file named LICENSE.
//
//		The unquoted strings t, true, f, false, and null are considered
//		literals and not strings.
//
//		Numbers are integers containing 1-8 decimal digit characters
//		Unquoted strings may contain any chars other than []{},: and whitespace.
//		" quoted strings may contain any chars other than " .
//		' quoted strings may contain any chars other than ' .
//		Comments are indicated with /* ... */ or // to end of line;
//			they are stripped out first and ignored when generating the
//			output object.
//
//		See https://tools.ietf.org/html/rfc7159,
//			https://tools.ietf.org/id/draft-ietf-jsonbis-rfc7159bis-04.html
//
//		The following structural characters are unchanged from JSON:
//				{ } [ ] : ,
//--------------------------------------------------------------------//
'use strict';

// This script must be early in list of script elements

// Global HUON version and default option values
var HUON={ver:'1.005',DisableTrailingCommas:0,MAX_NUMBER_CHARS_LEN:8};

// gHONGrammar={quotes:"\"'",valueSep:".",pairSep:",",quotesRequired:false,sepRequired:false,commentStart:"/*",commentEnd:"*/"}

//--------------------------------------------------------------------//
//		HUON: Convert HUstr to an Object
//--------------------------------------------------------------------//

// Hide all internal IDs except for the global HUON function object in
// an ignored closure value
!function()
	{
	// Global private vars for HUON, starting with 'g'
	var gStr,gSite,	// obj() args
		gws=" \t\r\n",	// Whitespace chars
		goff,			// Cur offset in gStr
		gstart,		// Start of cur string for backtracking
		gres;			// Function result

	// Public functions for HUON

	// Optionally specify error output to given string wrapped in an array
	HUON.init=init; // Public global function
	function init(Args)
		{
		//if (Args.ErrOutToString)
		//	gStrArr=Args.ErrOutToString;
		} // init

//--------------------------------------------------------------------//
//		HUON.obj: Public function to convert a HUON string to value
//--------------------------------------------------------------------//

	HUON.obj=obj2; // Public global function
	function obj2(Str,Site='',ErrOutArr=null)
		{
		try {return obj3(Str,Site);}
		catch(error) {HandleErr(error,ErrOutArr);}
		} // obj2

	// Private functions for HUON

	function HandleErr(error,OutArr)
		{
		if (OutArr)
			OutArr[0]=SafeHTML(error.message);
		else
			alert(error.message);
		} // HandleErr

	function obj3(Str,Site)
		{
		gStr=Str; gSite=Site;
		goff=0;
		var st=typeof gStr;
		if (st != 'string')
			Err('First argument to HUON.obj was of type '+st+', not string');
		removeComments();
		var val=value();
		skipSpace();
		if (goff>gStr.length)
			return Err('offset overrun at end of "'+gStr+'"');
		var remain=isAnythingAtEnd();
		if (remain)
			SyntaxErr(remain.length+' unrecognized character(s) found after a valid HUON substring');
		return val;
		} // obj3

	// Comment out "replace" lines here to
	// remove support for either kind of comments
	function removeComments()
		{
		//		"//"
		gStr=gStr.replace(/\/\/.*?$/mg,'');
		//		"/* ... */"
		gStr=gStr.replace(/\/\*[^\x05]*?\*\//mg,'');
		} // removeComments

	function value()
		{
		if (isAtNumber())
			return gres;
		if (isAtString())
			return gres;
		if (isAtArrayOrObj(true)) // Array
			return gres;
		if (isAtArrayOrObj(false)) // Object
			return gres;
		return SyntaxErr("A value was not found");
		} // value

	function isAtArrayOrObj(type/*false:Array,true:Object*/)
		{
		var left=type?'{':'[';
		var right=type?'}':']';
		var kind=type?'An object property':'An array element';
		var result; // Accumulated value
		var key,v;
		if (!isAt(left))
			return false;
		if (type)
			result={};
		else
			result=[];
		gres=result;
		if (isAt(right))
			return true; // Seen: [] Note: or {}
		// Seen: [
		for (;;) // Loop for each array or object element
			{
			// Append next array element or object property
			// Seen: [ | [value, | [value,value, | ...
			if (type)
				{
				if (!isAtString())
					return SyntaxErr("A property key was not found");
				key=gres;
				if (!isAt(":"))
					return SyntaxErr("A property key/value separator ':' was not found");
				v=value();
				result[key]=v;
				} // type=object
			else
				result.push(value()); // type=array

			if (HUON.DisableTrailingCommas)
				{
				// Seen: [value | [value,value | ...
				if (isAt(","))
					continue;
				} // DisableTrailingCommas

			else // Allow trailing commas
				{
				// Seen: [value | [value,value | ...
				if (isAt(","))
					{
					// Seen: [value, | [value,value, | ...
					if (!isAt(right))
						continue; // Append next value
					// Seen: [value,] | [value,value,] | ...
					gres=result;
					return true;
					} // isAt comma
				} // Allow trailing commas

			// Seen: [value | [value,value | ...
			if (!isAt(right))
				return SyntaxErr(kind+" was not terminated by ',' or '"+right+"'");
			// Seen: [value] | [value,value] | ...
			gres=result;
			return true;
			} // Loop for each array or object element
		} // isAtArrayOrObj

	// See if looking at a specific string,
	// skipping any whitespace first
	function isAt(str)
		{
		skipSpace();
		gstart=goff;
		if (!gStr.startsWith(str,goff))
			return false;
		goff+=str.length;
		return true;
		} // isAt

/*
	// See if looking at a specific unquoted string followed by a nonstring char
	function isAtSpecificStr(str)
		{
		gstart=goff;
		if (isAtPlainString() && gres===str)
			return true;
		goff=gstart;	// Backtrack on failure to match
		return false;
		} // isAtSpecificStr
*/

	// Skip any whitespace where allowed
	function skipSpace()
		{
		var t=gStr.substr(goff), tr=t.trimStart();
		goff+=(t.length-tr.length);
		} // skipSpace

	// Return remaining string, if any
	function isAnythingAtEnd()
		{
		return gStr.substring(goff);
		} // isAnythingAtEnd

	// Find a regexp pattern, with optional
	// paren subpat (first is 1)
	function isAtPattern(pattern,patNr=0,lenOutArr=null)
		{
		skipSpace();
		var arr=pattern.exec(gStr.substr(goff));
		if (!arr)
			return false;
		//D(arr);
		if (lenOutArr!==null)
			lenOutArr[0]=arr[0].length; // Optional: return entire pattern length
		goff+=arr[0].length; // Skip the entire pattern
		gres=arr[patNr]; // Result is just the paren subpat
		return true;
		} // isAtPattern

	// See if looking at number literal
	function isAtNumber()
		{
		var lenOutArr=[];
		if (!isAtPattern(/^[+-]?[0-9]+/,0,lenOutArr))
			return false;
		if (lenOutArr[0] > HUON.MAX_NUMBER_CHARS_LEN)
			SyntaxErr('A number contained more than '+HUON.MAX_NUMBER_CHARS_LEN+' characters');
		gres=parseInt(gres,10);
		return true;
		} // isAtNumber

	// See if looking at an unquoted string:
	// string not including []{},: and whitespace
	function isAtPlainString()
		{
		if (!isAtPattern(/^[^[\]{},: \n\t]+/))
		//if (!isAtPattern(/^[A-Za-z$_][A-Za-z$_0-9-]*/))
			return false;
		return true;
		} // isAtPlainString

	// See if looking at a string, whether quoted or not
	function isAtString()
		{
		// Quoted strings have priority
		if (isAtPattern(/^"(.*?)"/,1))
			return true;
		if (isAtPattern(/^'(.*?)'/,1))
			return true;

		// Unquoted strings and literals are also supported
		if (isAtPlainString())
			{
			handleLiteral();
			return true;
			}
		return false;
		} // isAtString

	function handleLiteral()
		{
		if (gres=='true')
			gres=true;
		else if (gres=='t')
			gres=true;
		else if (gres=='false')
			gres=false;
		else if (gres=='f')
			gres=false;
		else if (gres=='null')
			gres=null;
		//else if (gres=='HUON')
		//	SyntaxErr("HUON symbol");
		} // handleLiteral

	function SyntaxErr(msg) // Includes gStr with context arrow
		{
		if (typeof gStr != 'string')
			Err(msg);
		// Arrow candidates:
		// heavy vertical bar: %e2%9d%9a; right triangle: %E2%96%B6; Bright Button: \xf0\x9f\x94\x86;
		// dim button: \xf0\x9f\x94\x85
		var Arrow=decodeURIComponent("%f0%9f%94%85");
		var msg2='Syntax error: '+msg+' at "'+gStr.substring(0,goff)+Arrow+gStr.substring(goff)+'"';
		Err(msg2);
		} // SyntaxErr

	function Err(msg) // Raise an exception and exit
		{
		var msg2='*** '+msg;
		if (gSite)
			msg2+=', for HUON string with label: '+gSite;
		//console.log(msg2);
		//alert(msg2+n/*+new Error().stack*/);
		throw Error(msg2);
		} // Err

	function Stack()
		{
		return '\nStack: \n'+new Error().stack;
		} // Stack

/*
// Report an expression during debugging using alert()
function D(expr)
	{
	var t,v;
	if (Array.isArray(expr))
		t='array';
	else
		t=typeof expr;
	if (t=='undefined')
		v='';
	else
		{
		var v=JSON.stringify(expr, null, 4);
		if (typeof v=='undefined')
			v=expr.toString();
		}
	v='DEBUG: '+v+' ('+t+')';
	//if (typeof gStrArr=='undefined')
		alert(v);
	//else
	//	gStrArr[0]+=v+'<br>\n';
	return expr;
	} // D
*/

/*
//--------------------------------------------------------------------//
//		Simple database consisting of a global variable written to and
//		read from JSON stored in a file
//--------------------------------------------------------------------//

function ReadArr($File,&$Arr) // Read an array from a DB file
	{
	$Path=G::$DBDirPath."/$File";
	if (!file_exists($Path))
		Err("*Database file @ cannot be found",$Path);
	$Str=file_get_contents($Path,true);
	$Arr=json_decode($Str,true);
	if (is_null($Arr))
		Err("*Database file @ is corrupted and might be recoverable manually or from backup",$Path);
	} // ReadArr

function WriteArr($File,$Arr,$Author) // Write an array into a DB file
	{
	$Path=G::$DBDirPath."/$File";
	if (!file_exists($Path))
		Err("*Database file @ cannot be found",$Path);
	if (gettype($Arr)!='array')
		Err("*Argument has wrong type (WriteArr)");
	$Bak=$Path.".bak";
	if (copy($Path,$Bak)===false)
		Err("*Database file @ cannot be copied to backup @",$Path,$Bak);
	$Date=date("n/j/y g:iA");
	$Arr['Journal']="Updated by $Author at $Date";
	$Str=json_encode($Arr);
	$R=file_put_contents($Path,$Str);
	if ($R===false)
		Err("*Database file @ could not be written",$Path);
	} // WriteArr
*/
	}(); // End of ignored closure and its call
