#!/usr/bin/python
# brief : replace the all class widget
#
# This file is a part of NOALYSS under GPL
# Author D. DE BONTRIDDER danydb@aevalys.eu

from transform import *
import sys
import os
import glob

if len(sys.argv) == 1:
	print "you need one or more filename as argument"
	sys.exit(3)

files=glob.glob(sys.argv[1])
total=len(files)
print "Total file to handle %d " % (len(files))
for file in files:
	print "Working on file "+file
	new_file=file
	old_file=(file+'.arold')
	os.rename(new_file,old_file)
	fnew=open(new_file,'a+')
	fold=open(old_file)
	lines=fold.readlines()
	widget=('IHidden','IText','ISpan','ISelect','IDate','ICheckBox','IPoste','ICard','IFile','IRadio','ITextarea','IButton','IConcerned','ITva','ISearch')
	check={}
	for w in widget:
		check[w]=0
	require=""
	for line in lines:
		for w in widget:
			if check[w] == 1:
				continue
			if line.find(w) != -1 :
				require=require+'require_once("class_'+w.lower()+'.php");'+"\n"
				check[w]=1
	flag=0
	for line in lines:
		if line.find('require')!=-1 and flag == 0:
			fnew.write(require)
			flag=1
		fnew.write(line)
	fnew.close()
	fold.close()
	total=total-1
	print 'finished, remaining %d' % (total)
	
