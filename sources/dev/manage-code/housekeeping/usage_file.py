#!/usr/bin/python
# Check if the files in include are still used 
#
# This file is a part of NOALYSS under GPL
# Author D. DE BONTRIDDER danydb@aevalys.eu
from transform import *
import sys
import os
import glob
if len(sys.argv) < 2 :
	print "you need at least two arguments : the file name and the files "
	print " into you look"
	sys.exit(3)

filenames=glob.glob(sys.argv[1])
if len(filenames) == 0:
	filenames=[]
	filenames.append(sys.argv[1])
print str(filenames)+ "<-"+sys.argv[1]
for f in filenames:
	file_usage=[]
	filename=os.path.basename(f)
	
	for a in range(2,len(sys.argv)):
		files=glob.glob(sys.argv[a])
		print str(a)+" : "+sys.argv[a]
		reFunction=re.compile(filename)
		#reFunction=re.compile('(require|include|form).*'+filename,re.IGNORECASE)
		for file in files:
			#print "Working on file "+file
			fold=open(file)
			lines=fold.readlines()
			for line in lines:
				found=reFunction.findall(line)
				if len(found) != 0 :
					tmp={file:filename}
					file_usage.append(tmp)
					
			fold.close()
			#print 'finished, remaining %d' % (total)
	#print file_usage
	#print "lenght "+str(len(file_usage))
	if len (file_usage) > 0 :
		print "This file "+filename+" is used in "
		for x in file_usage:
			print x.keys()[0]
	else:
		print filename +" is never used "
		
