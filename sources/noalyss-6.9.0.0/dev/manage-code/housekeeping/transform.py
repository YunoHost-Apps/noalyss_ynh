#!/usr/bin/python
# brief : replace the all class widget
#
# This file is a part of NOALYSS under GPL
# Author D. DE BONTRIDDER danydb@aevalys.eu
import glob
import sys
import re
class Transform:
	widget=re.compile("new widget.*;.*")
	wegal=re.compile('.*=')
	wtext=re.compile("text",re.IGNORECASE)
	wselect=re.compile("select",re.IGNORECASE)
	wjsDate=re.compile("js_date",re.IGNORECASE)
	wHidden=re.compile("hidden",re.IGNORECASE)
	wcheckbox=re.compile("checkbox",re.IGNORECASE)
	wJSSearch_Poste=re.compile("js_search_poste",re.IGNORECASE)
	wJSSearch_only=re.compile("js_search_only",re.IGNORECASE)
	wSpan=re.compile("span",re.IGNORECASE)
	wFile=re.compile("file",re.IGNORECASE)
	wRadio=re.compile("radio",re.IGNORECASE)
	wButton=re.compile("button",re.IGNORECASE)
	wTextarea=re.compile("textarea",re.IGNORECASE)
	wJSConcerned=re.compile("js_concerned",re.IGNORECASE)
	wTva=re.compile("js_tva",re.IGNORECASE)
	wSearch=re.compile("js_search\"",re.IGNORECASE)
	string=""
	def __init__(self,p_string):
		self.string=p_string
	def transform(self):
		result=self.string
		found_widgets=self.widget.findall(self.string)
		if len(found_widgets) > 0:
			sEgal=self.wegal.findall(result)
			found_widget=found_widgets[0]
			result=""
			if len(self.wtext.findall(found_widget))>0:
				result="new IText"
			if len(self.wselect.findall(found_widget))>0:
				result="new ISelect"
			if len(self.wjsDate.findall(found_widget))>0:
				result="new IDate"
			if len(self.wHidden.findall(found_widget))>0:
				result="new IHidden"
			if len(self.wcheckbox.findall(found_widget))>0:
				result="new ICheckBox"
			if len(self.wJSSearch_Poste.findall(found_widget))>0:
				result="new IPoste"
			if len(self.wJSSearch_only.findall(found_widget))>0:
				result="new ICard"
			if len(self.wSpan.findall(found_widget))>0:
				result="new ISpan"
			if len(self.wFile.findall(found_widget))>0:
				result="new IFile"
			if len(self.wRadio.findall(found_widget))>0:
				result="new IRadio"
			if len(self.wTextarea.findall(found_widget))>0:
				result="new ITextArea"
			if len(self.wButton.findall(found_widget))>0:
				result="new IButton"
			if len(self.wJSConcerned.findall(found_widget))>0:
				result="new IConcerned"
			if len(self.wTva.findall(found_widget))>0:
				result="new ITva"
			if len(self.wSearch.findall(found_widget))>0:
				result="new ISearch"
			if result == "" :
				print "Invalid widget :"+self.string
				return 'INVALIDWIDGET '+self.string
			result=sEgal[0]+result
			reArg=re.compile('\(.*\)')
			content=reArg.findall(self.string)
			reSplit=re.compile(',')
			aArg=reSplit.split(content[0])
			if len(aArg) == 1 :
				return result+'();'+"\n"
			b=aArg[1:]
			virg=""
			arg=""
			for i in b:
				arg=arg+virg+i
				virg=','
			return result+'('+arg+';'+"\n"
		return result
			
	
if __name__ == "__main__":
	string=" $button_escape=new widget('button','Echapper');"
	a=Transform(string)
	print a.transform()
	
#
#	if len(sys.argv) < 1 :
#		print "Erreur pas de fichier comme argument"
#		sys.exit(1)
#	files=glob.glob(sys.argv[1])
#	for file in files:
#		lines=file.readlines()
#		for line in lines:
#			
