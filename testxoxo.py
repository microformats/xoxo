#!/usr/bin/python
# -*- coding: utf-8 -*-
"""testxoxo.py 
Unit tests for xoxo.py
This file tests the functions in xoxo.py 
The underlying model here is http://diveintopython.org/unit_testing/index.html 

run from command line with
python testxoxo.py -v
"""
import xoxo
reload(xoxo)
import unittest

class xoxoTestCases(unittest.TestCase):
    
    def testSimpleList(self):
        '''make a xoxo file from a list'''
        l = ['1','2','3']
        html = xoxo.toXOXO(l)
        self.assertEqual(html,'<ol class="xoxo"><li>1</li><li>2</li><li>3</li></ol>')
    def testNestedList(self):
        '''make a xoxo file from a list with a list in'''
        l = ['1',['2','3']]
        html = xoxo.toXOXO(l)
        self.assertEqual(html,'<ol class="xoxo"><li>1</li><li><ol><li>2</li><li>3</li></ol></li></ol>')

    def testDictionary(self):
        '''make a xoxo file from a dictionary'''
        d = {'test':'1','name':'Kevin'}
        html = xoxo.toXOXO(d)
        self.assertEqual(html,'<ol class="xoxo"><li><dl><dt>test</dt><dd>1</dd><dt>name</dt><dd>Kevin</dd></dl></li></ol>')

    def testSingleItem(self):
        '''make a xoxo file from a string'''
        l = "test"
        html = xoxo.toXOXO(l)
        self.assertEqual(html,'<ol class="xoxo"><li>test</li></ol>')

    def testWrapDiffers(self):
        '''make a xoxo file from a string with and without html wrapper and check they are different'''
        l = "test"
        html = xoxo.toXOXO(l)
        htmlwrap =  xoxo.toXOXO(l,addHTMLWrapper=True)
        self.failIfEqual(html,htmlwrap)

    def testWrapSingleItem(self):
        '''make a wrapped xoxo file from a string'''
        l = "test"
        html = xoxo.toXOXO(l,addHTMLWrapper=True)
        self.assertEqual(html,'''<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN
http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head profile=""><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body><ol class="xoxo"><li>test</li></ol></body></html>''')

    def testWrapItemWithCSS(self):
        '''make a wrapped xoxo file from a string'''
        l = "test"
        html = xoxo.toXOXO(l,addHTMLWrapper=True,cssUrl='reaptest.css')
        self.assertEqual(html,'''<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN
http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head profile=""><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><style type="text/css" >@import "reaptest.css";</style></head><body><ol class="xoxo"><li>test</li></ol></body></html>''')

    def testDictionaryRoundTrip(self):
        ''' make a dictionary into a xoxo file and back again; check it is the same'''
        d = {'test':'1','name':'Kevin'}
        html = xoxo.toXOXO(d)
        newd = xoxo.fromXOXO(html)
        self.assertEqual(d,newd)
        
    def testDictionaryWithURLRoundTrip(self):
        ''' make a dictionary wiht an url in into a xoxo file and back again; check it is the same'''
        d = {'url':'http://example.com','name':'Kevin'}
        html = xoxo.toXOXO(d)
        newd = xoxo.fromXOXO(html)
        self.assertEqual(d,newd)    
    def testNestedDictionaryRoundTrip(self):
        ''' make a dictionary with a dict in into a xoxo file and back again; check it is the same'''
        d = {'test':'1','inner':{'name':'Kevin'}}
        html = xoxo.toXOXO(d)
        newd = xoxo.fromXOXO(html)
        self.assertEqual(d,newd)
    def testNestedDictionaryWithURLRoundTrip(self):
        ''' make a dictionary with an url and a dict into a xoxo file and back again; check it is the same'''
        d = {'url':'http://example.com','inner':{'name':'Kevin'}}
        html = xoxo.toXOXO(d)
        newd = xoxo.fromXOXO(html)
        self.assertEqual(d,newd)
    def testNestedDictionariesWithURLsRoundTrip(self):
        ''' make a dictionary with an url and a dict with an url into a xoxo file and back again; check it is the same'''
        d = {'url':'http://example.com','inner':{'name':'Kevin','url':'http://slashdot.org'}}
        html = xoxo.toXOXO(d)
        newd = xoxo.fromXOXO(html)
        self.assertEqual(d,newd)
    def testListRoundTrip(self):
        ''' make a list into a xoxo file and back again; check it is the same'''
        l = ['3','2','1']
        html = xoxo.toXOXO(l)
        newdl= xoxo.fromXOXO(html)
        self.assertEqual(l,newdl)
    def testListofDictsRoundTrip(self):
        ''' make a list of Dicts into a xoxo file and back again; check it is the same'''
        l = ['3',{'a':'2'},{'b':'1','c':'4'}]
        html = xoxo.toXOXO(l)
        newdl= xoxo.fromXOXO(html)
        self.assertEqual(l,newdl)
    def testListofListsRoundTrip(self):
        ''' make a list of Lists into a xoxo file and back again; check it is the same'''
        l = ['3',['a','2'],['b',['1',['c','4']]]]
        html = xoxo.toXOXO(l)
        newdl= xoxo.fromXOXO(html)
        self.assertEqual(l,newdl)
    def testDictofListsRoundTrip(self):
        ''' make a dict with lists in into a xoxo file and back again; check it is the same'''
        d = {'test':['1','2'],
        'name':'Kevin',
        'nestlist':['a',['b','c']],
        'nestdict':{'e':'6','f':'7'}}
        html = xoxo.toXOXO(d)
        newd = xoxo.fromXOXO(html)
        self.assertEqual(d,newd)

    def testXOXOjunkInContainers(self):
        '''make sure text outside <li> etc is ignored'''
        d=xoxo.fromXOXO('<ol>bad<li><dl>worse<dt>good</dt><dd>buy</dd> now</dl></li></ol>')
        self.assertEqual(d,{'good': 'buy'})

    def testXOXOjunkInElements(self):
        '''make sure text within <li> but outside a subcontainer is ignored'''
        l=xoxo.fromXOXO('<ol><li>bad<dl><dt>good</dt><dd>buy</dd></dl>worse</li><li>bag<ol><li>OK</li></ol>fish</li></ol>')
        self.assertEqual(l,[{'good': 'buy'},['OK']])

    def testXOXOWithSpacesAndNewlines(self):
        '''unmung some xoxo with spaces in and check result is right'''
        xoxoSample= '''<ol class='xoxo'> 
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
</ol>'''
        d = xoxo.fromXOXO(xoxoSample)
        d2={'text':'item 1',
            'description':" This item represents the main point we're trying to make.",
            'url':'http://example.com/more.xoxo',
            'title':'title of item 1',
            'type':'text/xml',
            'rel':'help'
            }
        xoxoAgain = xoxo.toXOXO(d)
        self.assertEqual(d,d2)
        #this needs a smarter whitespace-sensitive comparison
        #self.assertEqual(xoxoSample,xoxoAgain)

    def testSpecialAttributeDecoding(self):
        '''unmung some xoxo with <a href=' rel= etc in and check result is right'''
        xoxoSample= '''<ol class='xoxo'> 
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
</ol>'''
        d = xoxo.fromXOXO(xoxoSample)
        smartxoxoSample= '''<ol class='xoxo'> 
  <li><a href="http://example.com/more.xoxo"
         title="title of item 1"
         type="text/xml"
         rel="help">item 1</a> 
<!-- note how the "text" property is simply the contents of the <a> element -->
  </li>
</ol>'''
        d2 = xoxo.fromXOXO(smartxoxoSample)
        self.assertEqual(d,d2)
    def testSpecialAttributeAndDLDecoding(self):
        '''unmung some xoxo with <a href=' rel= etc in plus a <dl> in the same item and check result is right'''
        xoxoSample= '''<ol class="xoxo"> 
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
</ol>'''
        d = xoxo.fromXOXO(xoxoSample)
        smartxoxoSample= '''<ol class="xoxo"> 
  <li><a href="http://example.com/more.xoxo"
         title="title of item 1"
         type="text/xml"
         rel="help">item 1</a> 
<!-- note how the "text" property is simply the contents of the <a> element -->
      <dl>
        <dt>description</dt>
          <dd> This item represents the main point we're trying to make.</dd>
      </dl>
  </li>
</ol>'''
        d2 = xoxo.fromXOXO(smartxoxoSample)
        self.assertEqual(d,d2)
    def testSpecialAttributeEncode(self):
        '''check it makes an <a href with a url parameter'''
        d={'url':'http://example.com/more.xoxo','title':'sample url','type':"text/xml",'rel':'help','text':'an example'}
        html=xoxo.toXOXO(d)
        expectedHTML= '<ol class="xoxo"><li><a href="http://example.com/more.xoxo" title="sample url" rel="help" type="text/xml" >an example</a></li></ol>' 
        self.assertEqual(html,expectedHTML)
        
    def testSpecialAttributeRoundTripFull(self):
        '''check it makes an <a href with a url parameter'''
        d={'url':'http://example.com/more.xoxo','title':'sample url','type':"text/xml",'rel':'help','text':'an example'}
        html=xoxo.toXOXO(d)
        self.assertEqual(d,xoxo.fromXOXO(html))
    def testSpecialAttributeRoundTripNoText(self):
        '''check it makes an <a href with a url parameter and no text attribute'''
        d={'url':'http://example.com/more.xoxo','title':'sample url','type':"text/xml",'rel':'help'}
        html=xoxo.toXOXO(d)
        self.assertEqual(d,xoxo.fromXOXO(html))
    def testSpecialAttributeRoundTripNoTextOrTitle(self):
        '''check it makes an <a href with a url parameter and no text or title attribute'''
        d={'url':'http://example.com/more.xoxo'}
        html=xoxo.toXOXO(d)
        self.assertEqual(d,xoxo.fromXOXO(html))
    def testAttentionRoundTrip(self):
        '''check nested <a> and <dl> and <a> are preserved'''
        kmattn='''<ol class="xoxo"><li><a href="http://www.boingboing.net/" title="Boing Boing Blog" >Boing Boing Blog</a><dl><dt>alturls</dt><dd><ol><li><a href="http://boingboing.net/rss.xml" >xmlurl</a></li></ol></dd><dt>description</dt><dd>Boing Boing Blog</dd></dl></li><li><a href="http://www.financialcryptography.com/" title="Financial Cryptography" >Financial Cryptography</a><dl><dt>alturls</dt><dd><ol><li><a href="http://www.financialcryptography.com/mt/index.rdf" >xmlurl</a></li></ol></dd><dt>description</dt><dd>Financial Cryptography</dd></dl></li><li><a href="http://hublog.hubmed.org/" title="HubLog" >HubLog</a><dl><dt>alturls</dt><dd><ol><li><a href="http://hublog.hubmed.org/index.xml" >xmlurl</a></li><li><a href="http://hublog.hubmed.org/foaf.rdf" >foafurl</a></li></ol></dd><dt>description</dt><dd>HubLog</dd></dl></li></ol>''';
        d = xoxo.fromXOXO(kmattn)
        newattn = xoxo.toXOXO(d)
        d2 = xoxo.fromXOXO(newattn)
        self.assertEqual(newattn,xoxo.toXOXO(d2))
        self.assertEqual(d,d2)
        self.assertEqual(kmattn,newattn)
        
    def testUnicodeRoundtrip(self):
        '''check unicode characters can go to xoxo and back'''
        src=unicode('Tantek \xc3\x87elik and a snowman \xe2\x98\x83','utf-8')
        html = xoxo.toXOXO(src)
        self.assertEqual(src,xoxo.fromXOXO(html))
    def testUtf8Roundtrip(self):
        '''check utf8 characters can go to xoxo and back'''
        src='Tantek \xc3\x87elik and a snowman \xe2\x98\x83'
        html = xoxo.toXOXO(src)
        self.assertEqual(src,xoxo.fromXOXO(html).encode('utf-8'))
    def testWindows1252Roundtrip(self):
        '''check 1252 characters can go to xoxo and back'''
        src='This is an evil\xa0space'
        html = xoxo.toXOXO(src)
        self.assertEqual(src,xoxo.fromXOXO(html).encode('windows-1252'))
if __name__ == "__main__":
    unittest.main()
else:
    runner = unittest.TextTestRunner()
    suite = unittest.makeSuite(xoxoTestCases,'test')
    runner.run(suite)

