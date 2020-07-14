//--------------------------------------------------------------------//
//		Tests for HUONTest.js
//		file://C:/Web/HUON/HUON-test-js.html
//		HUON.js is assumed already seen
//--------------------------------------------------------------------//

'use strict';

// HUON-test and HUON options
const debugEnableHTMLtableOutput=0;
HUON.DisableTrailingCommas=0;

//--------------------------------------------------------------------//
//		Define all tests
//--------------------------------------------------------------------//

if (1) // Change to 0 for developing a particular test below
	{
Tests=[

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
[31,'{\u{FF21}:\u{FF41},A:a}','{"\u{FF21}":"\u{FF41}","A":"a"}'],
[32,'{/*This is a comment*/\nA:a,B:b}','{"A":"a","B":"b"}'],
[33,"{A'x'B : c }  ",'{"A\'x\'B":"c"}'],
[34,'{"A\' \'B":c}','{"A\' \'B":"c"}'],
[35,"   {\" 'A:' \":\"b&'x\"}",'{" \'A:\' ":"b&\'x"}'],
['35a',"   {\" 'A:' \":b&'x}",'{" \'A:\' ":"b&\'x"}'],
['35b',"   {\" 'A:' \":b&\"x}",'{" \'A:\' ":"b&\\"x"}'],
[36,'   {    A : [ 0 /*, 1 */] /*,b:{c:c,\nd:d}*/ }','{"A":[0]}'],
[37,'S // Y','"S"'],
[38,'[1,\n//2,\n3]','[1,3]'],
[39,'<font></font>','"<font></font>"'],
[40,'[x²+y²-6,height/width]','["x²+y²-6","height/width"]'],
[41,'{pronoun:I/we,amount:h%}','{"pronoun":"I/we","amount":"h%"}'],
[42,'12345678','12345678'],

// Test cases for errors (using negative test nrs)
[-1,'','A value was not found at'],
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

// Test cases for objects without trailing commas
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
Tests=[	// Insert a proposed test here:

];
	}

//--------------------------------------------------------------------//
//		Global but private variables
//--------------------------------------------------------------------//

var n="\n", N="<br>\n", Tests, gNrTests=0, gNrFailed=0;
var H=''; // HUON test results HTML string
var out=''; // All HTML to store in the "out" div

var w=window;
var d=w.document;
var GetEl=d.getElementById.bind(d);

var title="Testing HUON.js, ver. "+HUON.ver;
GetEl('title').innerHTML=d.title=title;

//--------------------------------------------------------------------//
//		Do all tests
//--------------------------------------------------------------------//

// Start table with heading
H+='<table><tr><th>Test</th><th>Input</th><th>Result</th><th>Details</th></tr>';

// Do each test
for (var ix=0; ix<Tests.length; ix++)
	{
	// Start next table entry
	H+='<tr>';

	// Get test
	var arr=Tests[ix];

	// Do option setting
	if (arr[0]=='set')
		{
		var optionStr=arr[1], value=arr[2];
		HUON[optionStr]=value;
		H+=columns('','')+columns('','Setting option "'+optionStr+'" to '+value);
		continue;
		}

	// Or do test
	else
		T(arr[0],arr[1],arr[2]);

	// End table entry
	H+='</tr>';
	}

// End table
H+='</table>'+N;

// Output overall pass or fail
if (gNrFailed)
	out+='<span class=red>'+gNrFailed+' of the '+gNrTests+' tests failed.</span>'+N;
else
	out+='<span class=blue>All of the '+gNrTests+' tests passed.</span>'+N;

// Append view of table source for debugging
if (debugEnableHTMLtableOutput)
	H+='For debugging:'+N+SafeHTML(H).replace(/&lt;\/tr&gt;/g, '&lt;/tr&gt;<br>\n');

// Output table
out+=N+H;
GetEl('out').innerHTML=out;

// Test function
function T(nr, string, goalStr)
	{
	var value, ErrOutArr=[];
	var st=typeof string;
	gNrTests++;
	if (st!='string')
		{
		alert('The test string for test '+nr+' was a '+st+', not a string!');
		throw Error('Error in HUON test parameters');
		}
	if (typeof goalStr!='string')
		{
		alert('The goal string of test '+nr+' was not a string!');
		throw Error('Error in HUON test parameters');
		}
	if (!goalStr)
		{
		alert('The goal string of test '+nr+' cannot be an empty string!');
		throw Error('');
		}

	string=SafeHTML(string);
	goalStr=SafeHTML(goalStr);

	// Test and Input columns
	H+='<td>Test '+nr+'</td><td>'+string+'</td>';

	// Calculate and test the value
	value=HUON.obj(string,'',ErrOutArr); // site: 'test '+nr
	if (typeof ErrOutArr=='undefined' || ErrOutArr==null)
		alert('ERR 1'); // Cannot happen; worth checking
	if (nr < 0)
		{
		if (!ErrOutArr[0])
			H+=columns(failed(),'An expected error message containing "'+goalStr+'" was not generated');
		else if (ErrOutArr[0].indexOf(goalStr)==-1)
			// Goal not found in err msg
			H+=columns(failed(),'"'+goalStr+'" was not found in error message: '+ErrOutArr[0]);
		else
			// Goal found in err msg
			H+=columns(passed(),'Correctly generated: '+ErrOutArr[0]);
		}
	else
		{
		if (ErrOutArr[0])
			H+=columns(failed(),ErrOutArr[0]); // Error message in normal test
		else if (compareWithGoal(value,goalStr))
			H+=columns(passed(),'Correctly generated '+SafeHTML(JSON.stringify(value))+' ('+GetType(value)+')');
		}
	} // T

function columns(a,b)
	{
	return '<td class=resColor>'+a+'</td><td>'+b+'</td>';
	} // columns

function compareWithGoal(value,goalStr)
	{
	var at=typeof value, gt;
	var valstr=JSON.stringify(value);
	//var goalstr=JSON.stringify(goal);

	if (valstr == goalStr)
		return true;
	else
		{
		//gt=typeof goal;
		H+=columns(failed(),'Generated '+valstr+
			'('+at+') instead of '+goalStr+' as expected'); // ('+gt+')
		}
	return false;
	} // compareWithGoal

function passed()
	{
	return '<span class=blue>pass</span>';
	} // passed

function failed()
	{
	gNrFailed++;
	return '<span class=red>fail</span>';
	} // failed

function GetType(val)
	{
	if (Array.isArray(val))
		return 'array';
	return typeof val;
	} // GetType

function SafeHTML(html)
	{
	var at=typeof html;
	if (at!='string')
		alert('SafeHTML: Arg was of type '+at+' instead of string');
	return html.replace(/</g, "&lt;").replace(/>/g, "&gt;");
	} // SafeHTML
