#!/usr/bin/python
# brief = check if the files given in arguments using
# the -> check_dossier function
#
# This file is a part of NOALYSS under GPL
# Author D. DE BONTRIDDER danydb@aevalys.eu


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
	#print "Working on file "+file
	fold=open(file)
	lines=fold.readlines()
	widget=('new User','->check_dossier','->Check')
	check={}
	for w in widget:
		check[w]=0
	require=""
	for line in lines:
		for w in widget:
			if check[w] == 1:
				continue
			if line.find(w) != -1 :
				check[w]=1
	fold.close()
	for w in widget:
		if check[w] == 0 : 
			print  "Missing in "+file+"  "+w
	total=total-1
	#print 'finished, remaining %d' % (total)
	
