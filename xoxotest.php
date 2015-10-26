<?
include("xoxolib.php");
function assertEqual($testname,$str1,$str2)
{
if ($str1 == $str2)
    echo "<h3>&#x221a; $testname </h3>";
else 
    {
    echo "<h3><big>&#x2639;</big> $testname failed</h3>";
    echo "<dl><dt>expected</dt>\n<dd>$str1</dd>\n<dt>returned</dt>\n<dd>$str2</dd>\n<dl>";
    
    }
}

function assertArrayEqual($testname,$expected,$returned)
{
if ($expected == $returned)
    echo "<h3>&#x221a; $testname </h3>";
else 
    {
    echo "<h3><big>&#x2639;</big> $testname failed</h3>";
    echo "<dl><dt>expected</dt>\n<dd><pre>";
    var_dump($expected);
    echo "</pre></dd>\n<dt>returned</dt>\n<dd><pre>";
    var_dump($returned);
    echo "</pre></dd>\n<dl>";
    
    }
}

function failIfEqual($testname,$str1,$str2)
{
if ($str1 != $str2)
    echo "<h3>&#x221a; $testname</h3>";
else 
    {
    echo "<h3><big>&#x2639;</big> $testname failed</h3>";
    echo "<dl><dt>both were</dt><dd>$str1</dd><dl>";
    
    }
}


$l = array('1','2','3');
$html = toXOXO($l);
assertEqual('make xoxo from list','<ol class="xoxo"><li>1</li><li>2</li><li>3</li></ol>',$html);

$s = 'test';
$html = toXOXO($s);
assertEqual("make xoxo from a string",'<ol class="xoxo"><li>test</li></ol>',$html);
$htmlwrap = toXOXO($s,TRUE);
failIfEqual("make sure wrapped and unwrapped differ",html,htmlwrap);
assertEqual("make wrapped xoxo from a string",'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head profile=""><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body><ol class="xoxo"><li>test</li></ol></body></html>',$htmlwrap);
$csswrap = toXOXO($s,TRUE,"reaptest.css");
assertEqual("make wrapped xoxo with css link from a string",'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head profile=""><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><style type="text/css" >@import "reaptest.css";</style></head><body><ol class="xoxo"><li>test</li></ol></body></html>',$csswrap);
$l2 = array('1',array('2','3'));
$html = toXOXO($l2);
assertEqual('make xoxo from nested list','<ol class="xoxo"><li>1</li><li><ol><li>2</li><li>3</li></ol></li></ol>',$html);
$d = array(test=>'1');
$html = toXOXO($d);
assertEqual('make xoxo from 1-element dictionary','<ol class="xoxo"><li><dl><dt>test</dt><dd>1</dd></dl></li></ol>',$html);
$d = array(test=>'1',name=>Kevin);
$html = toXOXO($d);
assertEqual('make xoxo from dictionary','<ol class="xoxo"><li><dl><dt>test</dt><dd>1</dd><dt>name</dt><dd>Kevin</dd></dl></li></ol>',$html);
$d = array('url'=>'http://example.com/more.xoxo','title'=>'sample url','type'=>"text/xml",'rel'=>'help','text'=>'an example');
$html = toXOXO($d);
assertEqual('make xoxo from dictionary with url','<ol class="xoxo"><li><a href="http://example.com/more.xoxo" title="sample url" rel="help" type="text/xml" >an example</a></li></ol>',$html);
$d = array('url'=>'http://example.com/more.xoxo','title'=>'sample url','type'=>"text/xml",'rel'=>'help','text'=>'an example','thing'=>'and another thing...');
$html = toXOXO($d);
assertEqual('make xoxo from dictionary with url and thing','<ol class="xoxo"><li><a href="http://example.com/more.xoxo" title="sample url" rel="help" type="text/xml" >an example</a><dl><dt>thing</dt><dd>and another thing...</dd></dl></li></ol>',$html);
$d = array('url'=>'http://example.com/more.xoxo','title'=>'sample url','type'=>"text/xml",'rel'=>'help','text'=>'an example','list'=>array('and', 'another','thing...'));
$html = toXOXO($d);
assertEqual('make xoxo from dictionary with url and list','<ol class="xoxo"><li><a href="http://example.com/more.xoxo" title="sample url" rel="help" type="text/xml" >an example</a><dl><dt>list</dt><dd><ol><li>and</li><li>another</li><li>thing...</li></ol></dd></dl></li></ol>',$html);
$l = array('3',array('a'=>'2'));
$html = toXOXO($l);
assertEqual('make xoxo from dict in list','<ol class="xoxo"><li>3</li><li><dl><dt>a</dt><dd>2</dd></dl></li></ol>',$html);

$l = array('3','2','1');
$html = toXOXO($l);
$newdl= fromXOXO($html);
assertArrayEqual('list to xoxo and back',$l,$newdl);
$l = array('1',array('a','b'));
$html = toXOXO($l);
$newdl= fromXOXO($html);
assertArrayEqual('list of lists to xoxo and back',$l,$newdl);

$l= array('3',array('a','2'),array('b',array('1',array('c','4'))));
$html = toXOXO($l);
$newdl= fromXOXO($html);
assertArrayEqual('list of list of lists to xoxo and back',$l,$newdl);
$d = array(test=>'1',name=>Kevin);
$html = toXOXO($d);
$newd= fromXOXO($html);
assertArrayEqual('dictionary to xoxo and back',$d,$newd);

$l = array('3',array('a'=>'2'),array('b'=>'1','c'=>'4'));
$html = toXOXO($l);
$newdl= fromXOXO($html);
assertArrayEqual('list of dicts to xoxo and back',$l,$newdl);
assertEqual('list of dicts to xoxo and back',$html,toXOXO($newdl));
$l = array('one'=>array('a'=>'2','b'=>'3'),'two'=>array('c'=>'4'));
$html = toXOXO($l);
$newdl= fromXOXO($html);
assertArrayEqual('dict of dicts to xoxo and back',$l,$newdl);
assertEqual('dict of dicts to xoxo and back',$html,toXOXO($newdl));
$l = array('one'=>array('a'=>'2','b'=>'3'),'url'=>'http://example.com');
$html = toXOXO($l);
$newdl= fromXOXO($html);
assertArrayEqual('dict of dicts with url to xoxo and back',$l,$newdl);
assertEqual('dict of dicts with url to xoxo and back',$html,toXOXO($newdl));
$d = array('test'=> array('1','2'),
'name'=> 'Kevin','nestlist'=> array('a',array('b','c')),
'nestdict'=>array('e'=>'6','f'=>'7'));
$html = toXOXO($d);
$newd= fromXOXO($html);
assertArrayEqual('dictionary of lists  to xoxo and back',$d,$newd);

$d=fromXOXO('<ol>bad<li><dl>worse<dt>good</dt><dd>buy</dd> now</dl></li></ol>');
assertArrayEqual('make sure text outside &lt;li&gt; etc is ignored',array(good=>buy),$d);

$l=fromXOXO('<ol><li>bad<dl><dt>good</dt><dd>buy</dd></dl>worse</li><li>bag<ol><li>OK</li></ol>fish</li></ol>');
assertArrayEqual('make sure text within &lt;li&gt; but outside a subcontainer is ignored',array(array(good=>buy),array('OK')),$l);

$xoxoSample= "<ol class='xoxo'> 
  <li>
    <dl>
        <dt>text</dt>
        <dd>item 1</dd>
        <dt>description</dt>
        <dd> This item represents the main point we're trying to make.</dd>
        <dt>url</dt>
        <dd>http://example.com/more.xoxo</dd>
        <dt>title</dt>
        <dd>title of item 1</dd>
        <dt>type</dt>
        <dd>text/xml</dd>
        <dt>rel</dt>
        <dd>help</dd>
    </dl>
  </li>
</ol>";
$d = fromXOXO($xoxoSample);
$d2=array('text'=>'item 1',
    'description'=>" This item represents the main point we're trying to make.",
    'url'=>'http://example.com/more.xoxo',
    'title'=>'title of item 1',
    'type'=>'text/xml',
    'rel'=>'help');
assertArrayEqual('unmung some xoxo with spaces in and check result is right',$d2,$d);

$xoxoSample= "<ol class='xoxo'> 
  <li>
    <dl>
        <dt>text</dt>
        <dd>item 1</dd>
        <dt>url</dt>
        <dd>http://example.com/more.xoxo</dd>
        <dt>title</dt>
        <dd>title of item 1</dd>
        <dt>type</dt>
        <dd>text/xml</dd>
        <dt>rel</dt>
        <dd>help</dd>
    </dl>
  </li>
</ol>";
$d = fromXOXO($xoxoSample);
$smartxoxoSample= "<ol class=\"xoxo\"> 
  <li><a href=\"http://example.com/more.xoxo\"
         title=\"title of item 1\"
         type=\"text/xml\"
         rel=\"help\">item 1</a> 
<!-- note how the \"text\" property is simply the contents of the <a> element -->
  </li>
</ol>";
$d2 = fromXOXO($smartxoxoSample);
assertArrayEqual('unmung some xoxo with &lt;a href= rel= etc in and check result is right',$d,$d2);
$xoxoSample= "<ol class='xoxo'> 
  <li>
    <dl>
        <dt>text</dt>
        <dd>item 1</dd>
        <dt>description</dt>
        <dd> This item represents the main point we're trying to make.</dd>
        <dt>url</dt>
        <dd>http://example.com/more.xoxo</dd>
        <dt>title</dt>
        <dd>title of item 1</dd>
        <dt>type</dt>
        <dd>text/xml</dd>
        <dt>rel</dt>
        <dd>help</dd>
    </dl>
  </li>
</ol>";
$d = fromXOXO($xoxoSample);
$smartxoxoSample= "<ol class=\"xoxo\"> 
  <li><a href=\"http://example.com/more.xoxo\"
         title=\"title of item 1\"
         type=\"text/xml\"
         rel=\"help\">item 1</a> 
<!-- note how the \"text\" property is simply the contents of the <a> element -->
      <dl>
        <dt>description</dt>
          <dd> This item represents the main point we're trying to make.</dd>
      </dl>
  </li>
</ol>";
$d2 = fromXOXO($smartxoxoSample);
assertArrayEqual('unmung some xoxo with &lt;a href= rel= etc in and check result is right',$d,$d2);

$d=array('url'=>'http://example.com/more.xoxo','title'=>'sample url','type'=>"text/xml",'rel'=>'help','text'=>'an example');
$html=toXOXO($d);
assertArrayEqual('round trip url to href to url',$d,fromXOXO($html));

$d=array('url'=>'http://example.com/more.xoxo','title'=>'sample url','type'=>"text/xml",'rel'=>'help');
$html=toXOXO($d);
assertArrayEqual('round trip url to href to url (no text)',$d,fromXOXO($html));

$d=array('url'=>'http://example.com/more.xoxo');
$html=toXOXO($d);
assertArrayEqual('round trip url to href to url (just url)',$d,fromXOXO($html));
$kmattn=<<<ENDATTN
<ol class="xoxo"><li><a href="http://www.boingboing.net/" title="Boing Boing Blog" >Boing Boing Blog</a><dl><dt>alturls</dt><dd><ol><li><a href="http://boingboing.net/rss.xml" >xmlurl</a></li></ol></dd><dt>description</dt><dd>Boing Boing Blog</dd></dl></li><li><a href="http://www.financialcryptography.com/" title="Financial Cryptography" >Financial Cryptography</a><dl><dt>alturls</dt><dd><ol><li><a href="http://www.financialcryptography.com/mt/index.rdf" >xmlurl</a></li></ol></dd><dt>description</dt><dd>Financial Cryptography</dd></dl></li><li><a href="http://hublog.hubmed.org/" title="HubLog" >HubLog</a><dl><dt>alturls</dt><dd><ol><li><a href="http://hublog.hubmed.org/index.xml" >xmlurl</a></li><li><a href="http://hublog.hubmed.org/foaf.rdf" >foafurl</a></li></ol></dd><dt>description</dt><dd>HubLog</dd></dl></li></ol>
ENDATTN;
$d=fromXOXO($kmattn);
$newattn = toXOXO($d);
$d2=fromXOXO($newattn);
assertArrayEqual('attention double round-trip',$d,$d2);
assertEqual('attention triple round-trip',$newattn,toXOXO($d2));
assertEqual('attention one round-trip',$kmattn,$newattn);
$d=array(array(url=>"http://www.boingboing.net/",title=>"Boing Boing Blog","alturls"=>array(array("url"=>"http://boingboing.net/rss.xml","text"=>
"xmlurl")),"description"=>"Boing Boing Blog"),array(url=>"http://www.financialcryptography.com/",title=>"Financial Cryptography","alturls"=>array(array("url"=>"http://www.financialcryptography.com/mt/index.rdf","text"=>
"xmlurl")),"description"=>"Financial Cryptography"));
$attn=<<<ENDATTN
<ol class="xoxo"><li><a href="http://www.boingboing.net/" title="Boing Boing Blog" >Boing Boing Blog</a><dl><dt>alturls</dt><dd><ol><li><a href="http://boingboing.net/rss.xml" >xmlurl</a></li></ol></dd><dt>description</dt><dd>Boing Boing Blog</dd></dl></li><li><a href="http://www.financialcryptography.com/" title="Financial Cryptography" >Financial Cryptography</a><dl><dt>alturls</dt><dd><ol><li><a href="http://www.financialcryptography.com/mt/index.rdf" >xmlurl</a></li></ol></dd><dt>description</dt><dd>Financial Cryptography</dd></dl></li></ol>
ENDATTN;
assertEqual('attention encode',$attn,toXOXO($d));


?>
