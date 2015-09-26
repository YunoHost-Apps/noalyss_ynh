#!/usr/bin/python
# Check if a function is still used
#
# This file is a part of NOALYSS under GPL
# Author D. DE BONTRIDDER danydb@aevalys.eu

from transform import *
import sys
import os
import glob
if len(sys.argv) < 2 :
	print "you need at least one argument : the file containing the function"
	print " the second and the thirst are the files where you look for those functions"
	sys.exit(3)

files=glob.glob(sys.argv[1])
total=len(files)
print "Total file to handle %d " % (len(files))
reFunction=re.compile('(function) (\w+).*\((.*)\)')
function_name=[]
fList=open("function.txt","a+")
for file in files:
	print "Working on file "+file
	if file.find('class_') != -1:
		continue
	fold=open(file)
	lines=fold.readlines()
	for line in lines:
		found=reFunction.findall(line)
		if len(found) != 0 :
			fctname=found[0][1]
			tmp={file:fctname}
			function_name.append(tmp)
			fList.write(file + ";" + fctname+"\n")
	fold.close()
	total=total-1
	print 'finished, remaining %d' % (total)
fList.close()
if len(sys.argv) == 2 :
	print "the fonctions are "
	for e in function_name:
		fct=e.values()[0]
		print fct

used={}
for e in function_name:
		fct=e.values()[0]
		used[fct]=0
for a in range(2,len(sys.argv)):
	print str(a)+ ': '+sys.argv[a]
	files_target=glob.glob(sys.argv[a])
	
	for e in function_name:
		fct=e.values()[0]
		for file in files_target:
			fd=open(file)
			buffer=fd.readlines()
			for line in buffer:
				if line.find(fct)!= -1:
					used[fct]=used[fct]+1
			fd.close()
		
		
for u in used.keys():
	print "%s : %d " % ( u,used[u])
