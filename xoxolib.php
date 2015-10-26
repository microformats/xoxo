<?
function getKind($struct)
{
    if (!is_array($struct)) return 'string';
    if (!isset($struct[0]))
        $result = 'dictionary';
    else if (array_keys($struct)==range(0,count($struct)-1))
        $result = 'list';
    else
        $result = 'dictionary';
    return $result;
}
function makeXOXO($struct,$className='')
{
    $s='';
    $kind = getKind($struct);
    #echo "$kind:\n";
    #var_dump($struct);
    if ($kind=='list')
        {
        if ($className)
            $s .= "<ol class=\"$className\">";
        else 
            $s .= "<ol>";
        foreach ($struct as $key => $value)
            $s .= "<li>" . makeXOXO($value) ."</li>";
        $s .="</ol>";
        }
    else if ($kind=='dictionary')
        {
        if (isset($struct['url']))
            {
            $s .='<a href="' .$struct['url']. '" ';
            if (isset($struct['text']))
                $text= $struct['text'];
            else if (isset($struct['title']))
                $text= $struct['title'];
            else
                $text= $struct['url'];
            foreach (array('title','rel','type') as $attr)
                if (isset($struct[$attr]))
                    {
                    $s .= "$attr=\"" . $struct[$attr] .'" ';
                    unset($struct[$attr]);
                    }
            $s .= ">" . makeXOXO($text) ."</a>";
            unset($struct['url'],$struct['text']);
            }
        if (count($struct))
            {
            $s .="<dl>";
            foreach ($struct as $key => $value)
                $s .= "<dt>$key</dt><dd>". makeXOXO($value) . "</dd>";
            $s .= "</dl>";
            }
        }
    else
        $s .= "$struct";
    #echo "returned $s\n";
    return $s;
}
function toXOXO($struct,$addHTMLWrapper=FALSE,$cssUrl='')
{
    if (getKind($struct) != 'list')
        $struct = array($struct);
    $xoxo = makeXOXO($struct,'xoxo');
    if ($addHTMLWrapper)
        {
        $s= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head profile=""><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        if ($cssUrl) $s .="<style type=\"text/css\" >@import \"$cssUrl\";</style>";
        $s .="</head><body>$xoxo</body></html>";
        return $s;
        }
    return $xoxo;
}


function pushStruct($struct,&$structstack,&$xostack,$structType)
{
    if (is_array($struct) && $structType=='dict' && count($structstack) && is_array(end($structstack)) && isset($structstack[count($structstack)-1]['url']) && end($structstack) != end($xostack))
        $xostack[] = &$structstack[count($structstack)-1]; # put back the <a>-made one for extra def's
    else
        {
        $structstack[]=$struct;
        $xostack[]=&$structstack[count($structstack)-1];
        }
}

function fromXOXO($html)
{
    $structs=array();
    $xostack=array();
    $textstack=array('');
    $dumpStacks=0;
    $p = xml_parser_create();
    xml_parse_into_struct($p, $html, $xoxoVals, $xoxoIndex);
    xml_parser_free($p);

  if($dumpStacks)
        {
        echo "<pre>";
        var_dump($xoxoVals);
        var_dump($xoxoIndex);
        echo "</pre>"; 
        }
    $howmany = sizeof($xoxoVals);
    
    #echo "<pre>";
    $x = $xoxoIndex['OL'];
    for ($x=0;$x<$howmany;++$x)
        {
        if ($xoxoVals[$x]['tag'] == 'OL' || $xoxoVals[$x]['tag'] == 'DL'|| $xoxoVals[$x]['tag'] == 'UL')
            {
            if ($xoxoVals[$x]['tag'] == 'DL')
                $structType = 'dict';
            else 
                $structType = 'list';
            if ($xoxoVals[$x]['type'] == 'open')
                pushStruct(array(),$structs,$xostack,$structType);
            if ($xoxoVals[$x]['type'] == 'close')
                array_pop($xostack);
            if($dumpStacks)
                {
                echo $xoxoVals[$x]['type'] .' ' . $xoxoVals[$x]['tag'] .":\n";
                var_dump($structs);
                var_dump($xostack);
                }
            }
        if ($xoxoVals[$x]['tag'] == 'LI')
            {
            if ($xoxoVals[$x]['type'] == 'complete')
               array_push($xostack[count($xostack)-1],$xoxoVals[$x]['value']);
            if ($xoxoVals[$x]['type'] == 'close')
                {
                array_push($xostack[count($xostack)-1],array_pop($structs));
                }
            if($dumpStacks)
                {
                echo $xoxoVals[$x]['type'] .' ' . $xoxoVals[$x]['tag'] .":\n";
                var_dump($structs);
                var_dump($xostack);
                }
            }

        if ($xoxoVals[$x]['tag'] == 'DT')
            {
            if ($xoxoVals[$x]['type'] == 'complete')
                array_push($textstack,$xoxoVals[$x]['value']);
            }
        if ($xoxoVals[$x]['tag'] == 'DD')
            {
            if ($xoxoVals[$x]['type'] == 'complete')
                {
                $key = array_pop($textstack);
                $xostack[count($xostack)-1][$key] = $xoxoVals[$x]['value'];
                }
            if ($xoxoVals[$x]['type'] == 'close')
                {
                $key = array_pop($textstack);
                $xostack[count($xostack)-1][$key] =array_pop($structs);
                }
          if($dumpStacks)
                {
                echo $xoxoVals[$x]['type'] .' ' . $xoxoVals[$x]['tag'] .":\n";
                var_dump($structs);
                var_dump($xostack);
                }
            }
        if ($xoxoVals[$x]['tag'] == 'A')
            {
            if ($xoxoVals[$x]['type'] == 'complete')
                {
                $attrs = $xoxoVals[$x]['attributes'];
                $dict=array();
                foreach ($attrs as $key=> $value)
                    {
                    if ($key=='HREF')
                        $dict['url'] = $value;
                    else
                        $dict[strtolower($key)] = $value;
                    }
                $val = $xoxoVals[$x]['value'];
                if (isset($val) && ($val != $dict['title']) && ($val != $dict['url']))
                    $dict['text'] = $val;
                pushStruct($dict,$structs,$xostack,'dict');
                array_pop($xostack);
 
                if($dumpStacks)
                    {
                    echo $xoxoVals[$x]['type'] .' ' . $xoxoVals[$x]['tag'] .":\n";
                    var_dump($structs);
                    var_dump($xostack);
                    }
               }
            }
        }
     #echo "</pre>";
   while (count($structs) == 1 && getKind($structs) == 'list')
        $structs = $structs[0];
    return $structs;
}
?>
