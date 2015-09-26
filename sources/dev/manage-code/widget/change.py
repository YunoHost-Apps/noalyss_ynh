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
	old_file=(file+'.old')
	os.rename(new_file,old_file)
	fnew=open(new_file,'a+')
	fold=open(old_file)
	lines=fold.readlines()
	for line in lines:
		buf=Transform(line)
		fnew.write(buf.transform())
	fnew.close()
	fold.close()
	total=total-1
	print 'finished, remaining %d' % (total)
	
