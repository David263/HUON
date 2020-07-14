<?php
//--------------------------------------------------------------------//
//		HUON-test.php: David Spector, 6/20/20, version 1.00
//		See HUON.js for initial comments (include here)
//		http://localhost/HUON/HUON-test.php
//
//		Copyright © 2020 Springtime Software
//		The terms of the license and copyright for this software are as
//		specified in the included file named LICENSE.
//--------------------------------------------------------------------//
// For debugging:
define("debugEnableHTMLtableOutput",0);

define("ENABLE_STACK_ON_ERROR",0);

// Report an expression during debugging
function D($val)
	{
	exit('(D='.json_encode($val,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).':'.gettype($val).')');
	} // D

require "HUON.php";

//--------------------------------------------------------------------//
//		Define all tests
//--------------------------------------------------------------------//
if (1) // Change to 0 for developing a particular test below
	{
$Tests=[
[1,'true','true'],
[2,'t','true'],
[3,'false','false'],
[4,'f','false'],
[5,'null','null'],
[6,'79','79'],
[7,'xyz','"xyz"'],
[8,'"xy,z"','"xy,z"'],
[9,"'xy,z'",'"xy,z"'],
[10,'[]','[]'],
[11,'[null]','[null]'],
[12,'[true]','[true]'],
[13,'truex','"truex"'],
[14,'tru','"tru"'],
[15,'"t"','"t"'],
[16,'[t,t,t,t,t,t,t,t,t,t,f]','[true,true,true,true,true,true,true,true,true,true,false]'],
[17,'[t,79,xyz]','[true,79,"xyz"]'],
[18,'{}','{}'],
[19,'{a:null}','{"a":null}'],
[20,'{a:3}','{"a":3}'],
[21,'{a:"b"}','{"a":"b"}'],
[22,"{a:'b'}",'{"a":"b"}'],
[23,'{"a":"b"}','{"a":"b"}'],
[24,'{0:b,1:d}','{"0":"b","1":"d"}'],
[25,'{a:[x,78]}','{"a":["x",78]}'],
[26,'{a:{b:3}}','{"a":{"b":3}}'],
[27,'[{b:3}]','[{"b":3}]'],
[28,'[[3]]','[[3]]'],
[29,'[[2,3],[4,5]]','[[2,3],[4,5]]'],
[30,'    test	','"test"'],
[31,"{\xe2\x9c\xa8:\xf0\x9f\x8d\xb5}","{\"\xe2\x9c\xa8\":\"\xf0\x9f\x8d\xb5\"}"],
[32,'{A:/*This is a comment\n*/a,/*xy*/B:b}','{"A":"a","B":"b"}'],
[33,"{A'x'B : c }  ",'{"A\'x\'B":"c"}'],
[34,'{"A\' \'B":c}','{"A\' \'B":"c"}'],
[35,"   {\" 'A:' \":\"b&'x\"}",'{" \'A:\' ":"b&\'x"}'],
['35a',"   {\" 'A:' \":b&'x}",'{" \'A:\' ":"b&\'x"}'],
['35b',"   {\" 'A:' \":b&\"x}",'{" \'A:\' ":"b&\\"x"}'],
[36,'   {    A : [ 0 /*, 1 */] /*,b:{c:c,\nd:d}*/ }','{"A":[0]}'],
[37,'S // Y','"S"'],
[38,"[1,\n//2,\n3]",'[1,"//2",3]'],
[39,'<font></font>','"<font></font>"'],
[40,'[x²+y²-6,height/width]','["x²+y²-6","height/width"]'],
[41,'{pronoun:I/we,amount:h%}','{"pronoun":"I/we","amount":"h%"}'],
[42,'12345678','12345678'],

// Test cases for errors (using negative test nrs)
[-1,'','A value was not found at "'],
[-2,'33x','1 unrecognized character(s) found after a valid HUON substring'],
[-3,'[,]','A value was not found at "['],
[-4,'null[b','2 unrecognized character(s) found'],
[-5,']','A value was not found at "'],
[-6,'[33','An array element was not terminated by \',\' or \']\' at "[33'],
[-7,'{[]}','A property key was not found at "{'],
[-8,'{a 3}','A property key/value separator \':\' was not found at "{a'],
[-9,'{a:3','An object property was not terminated by \',\' or \'}\' at "{a:3'],
[-10,'[3 x 4]','An array element was not terminated by \',\' or \']\' at "[3'],
[-11,'{a:3 x b:4}','An object property was not terminated by \',\' or \'}\' at "{a:3'],
[-12,'a b','1 unrecognized character(s) found after a valid HUON substring at "a'],
[-13,'<font color=red>XX</font>','28 unrecognized character(s) found after a valid HUON substring at "<font'],
[-14,'{pronoun:I/we,amount:50%}','An object property was not terminated by \',\' or \'}\' at "{pronoun:I/we,amount:50'],
[-15,'[1,//2\n,\n3]','A value was not found at "[1,'],
[-16,'}{','A value was not found at "'],
[-17,'1.231239100000375e+46','20 unrecognized character(s) found after a valid HUON substring at "1'],
[-18,'123456789','A number contained more than 8 characters at "123456789'],

// Test cases for arrays without trailing commas
['set','DisableTrailingCommas',1],
[-70,'[','A value was not found at "['],
[-71,'[a','An array element was not terminated by \',\' or \']\' at "[a'],
[-72,'[,a','A value was not found at "['],
[-73,'[a,','A value was not found at "[a,'],
[-74,'[a,]','A value was not found at "[a,'],
[-75,'[a,b,]','A value was not found at "[a,b,'],
[-76,'[a,,]','A value was not found at "[a,'],
[-77,'[a,,b]','A value was not found at "[a,'],
[-78,'[a,b{}]','An array element was not terminated by \',\' or \']\' at "[a,b'],

// Test cases for arrays with trailing commas
['set','DisableTrailingCommas',0],
[-80,'[','A value was not found at "['],
[-81,'[a','An array element was not terminated by \',\' or \']\' at "[a'],
[-82,'[,a','A value was not found at "['],
[-83,'[a,','A value was not found at "[a,'],
[84,'[a,]','["a"]'],
[85,'[a,b,]','["a","b"]'],
[-86,'[a,,]','A value was not found at "[a,'],
[-87,'[a,,b]','A value was not found at "[a,'],
[-88,'[a,b{}]','An array element was not terminated by \',\' or \']\' at "[a,b'],

['set','DisableTrailingCommas',1],
[-90,'{','A property key was not found at "{'],
[-91,'{a:x','An object property was not terminated by \',\' or \'}\' at "{a'],
[-92,'{,a:x','A property key was not found at "{'],
[-93,'{a:x,','A property key was not found at "{a:x,'],
[-94,'{a:x,}','A property key was not found at "{a:x,'],
[-95,'{a:x,b:y,}','A property key was not found at "{a:x,b:y,'],
[-96,'{a:x,,}','A property key was not found at "{a:x,'],
[-97,'{a:x,,b:y}','A property key was not found at "{a:x,'],
[-98,'{a:x,b:y{}}','An object property was not terminated by \',\' or \'}\' at "{a:x,b:y'],
[-99,'{,}','A property key was not found at "{'],

// Test cases for objects with trailing commas
['set','DisableTrailingCommas',0],
[-100,'{','A property key was not found at "{'],
[-101,'{a:x','An object property was not terminated by \',\' or \'}\' at "{a'],
[-102,'{,a:x','A property key was not found at "{'],
[-103,'{a:x,','A property key was not found at "{a:x,'],
[104,'{a:x,}','{"a":"x"}'],
[105,'{a:x,b:y,}','{"a":"x","b":"y"}'],
[-106,'{a:x,,}','A property key was not found at "{a:x,'],
[-107,'{a:x,,b:y}','A property key was not found at "{a:x,'],
[-108,'{a:x,b:y{}}','An object property was not terminated by \',\' or \'}\' at "{a:x,b:y'],
[-109,'{,}','A property key was not found at "{'],
];
	}
else
	{
$Tests=[
// Insert a particular test here:

];
	}

//--------------------------------------------------------------------//
//		Global but private variables
//--------------------------------------------------------------------//

define("n","\n");
define("N","<br>\n");
$NrTests=0; $NrFailed=0;
$H=''; // HUON test results HTML string
$out=''; // All HTML to store in the "out" div

$title="Testing HUON.php, ver. ".HUON::$ver;

//--------------------------------------------------------------------//
//		Do all tests
//--------------------------------------------------------------------//

// Start table with heading
$H.='<table><tr><th>Test</th><th>Input</th><th>Result</th><th>Details</th></tr>';

// Do each test
for ($ix=0; $ix<count($Tests); $ix++)
	{
	// Start next table entry
	$H.='<tr>';

	// Get next $Tests entry
	$arr=$Tests[$ix];

	// Do option setting
	if ($arr[0]=='set')
		{
		$optionStr=$arr[1]; $value=$arr[2];
		HUON::$$optionStr=$value;
		$H.=columns('','').columns('','Setting option "'.$optionStr.'" to '.$value);
		continue;
		}

	// Or do test
	else
		T($arr[0],$arr[1],$arr[2]);

	// End table entry
	$H.='</tr>';
	}

// End table
$H.='</table>'.N;

// Output overall pass or fail
if ($NrFailed)
	$out.='<span class=red>'.$NrFailed.' of the '.$NrTests.' tests failed.</span>'.N;
else
	$out.='<span class=blue>All of the '.$NrTests.' tests passed.</span>'.N;

// Append view of table source for debugging
if (debugEnableHTMLtableOutput)
	$H.='For debugging:'.str_replace('&lt;/tr&gt;','&lt;/tr&gt;<br>\n',SafeHTML($H));

// Output table
$out.=N.$H;

// Test function
function T($nr, $string, $goalStr)
	{
	global $H, $NrTests, $NrFailed;
	$ErrOutStr=''; // Output from HUON::obj if an error occurred, else ''
	$NrTests++;
	$st=GetType($string);
	if ($st!='string')
		{
		Err('Error in HUON test parameters: The test string '.$string.' for test '.$nr.' was a '.$st.', not a string!');
		}
	if (GetType($goalStr)!='string')
		{
		Err('Error in HUON test parameters: The goal string of test '.$nr.' was not a string!');
		}
	if (!$goalStr)
		{
		Err('The goal string of test '.nr.' cannot be an empty string!');
		}

	$string=SafeHTML($string);
	$goalStr=SafeHTML($goalStr);

	// Test and Input columns
	$H.='<td>Test '.$nr.'</td><td>'.$string.'</td>';

	// Calculate and test the value
	$value=HUON::obj($string,'',$ErrOutStr); // site: 'test '.$nr
	//D($value);
	//D($ErrOutStr);
	if (GetType($ErrOutStr)=='undefined' || $ErrOutStr===null)
		{
		//D(GetType($ErrOutStr));
		Err('ERR T1'); // Cannot happen; worth checking
		}
	if ($nr < 0)
		{
		if (!$ErrOutStr)
			$H.=columns(failed(),'An expected error message containing "'.$goalStr.'" was not generated');
		else if (strpos($ErrOutStr,$goalStr)===false)
			// Goal not found in err msg
			$H.=columns(failed(),'"'.$goalStr.'" was not found in error message: '.$ErrOutStr);
		else
			// Goal found in err msg
			$H.=columns(passed(),'Correctly generated: '.$ErrOutStr);
		}
	else
		{
		if ($ErrOutStr)
			$H.=columns(failed(),$ErrOutStr); // Error message in normal test
		else if (compareWithGoal($value,$goalStr))
			$H.=columns(passed(),'Correctly generated '.
				SafeHTML(json_encode($value,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)).' ('.GetType($value).')');
		}
	} // T

function columns($a,$b)
	{
	return '<td class=resColor>'.$a.'</td><td>'.$b.'</td>';
	} // columns

function compareWithGoal($value,$goalStr)
	{
	global $H;
	$at=GetType($value);
	$valstr=json_encode($value,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
	//var goalstr=json_encode(goal,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

	if ($valstr == $goalStr)
		return true;
	else
		{
		//gt=GetType(goal);
		$H.=columns(failed(),'Generated '.$valstr.
			'('.$at.') instead of '.$goalStr.' as expected'); // ('.$gt.')
		}
	return false;
	} // compareWithGoal

function passed()
	{
	return '<span class=blue>pass</span>';
	} // passed

function failed()
	{
	global $NrFailed;
	$NrFailed++;
	return '<span class=red>fail</span>';
	} // failed

/*
function GetType($val)
	{
	if (Array.isArray($val))
		return 'array';
	return GetType($val);
	} // GetType
*/

function SafeHTML($html)
	{
	$at=gettype($html);
	if ($at!='string')
		Err('SafeHTML: Arg was of type '.$at.' instead of string');
	return str_replace(['<','>'],['&lt;','&gt;'],$html);
	} // SafeHTML

// Easier global variables
// If global doesn't exist, it is defined as null
function G($a,$b=null)
	{
	if (!isset($GLOBALS[$a]))
		$GLOBALS[$a]=null;
	if ($b===null)
		return $GLOBALS[$a];
	else
		$GLOBALS[$a]=$b;
	} // G

function Err($msg)
	{
	exit("*** $msg.");
	} // Err

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
<title><?=$title?></title>
<!-- <link rel="icon" href="favicon.ico"> -->
<link rel=stylesheet type="text/css" href="HUON-test.css">
</head>
<body>
<h2><?=$title?></h2>
<h3><div id=out><?=$out?></div><h3>
<p>Note: Negative test numbers test error messages instead of the value returned from HUON::obj().</p>
</body>
</html>
