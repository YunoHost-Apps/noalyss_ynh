#!/usr/bin/python
#-*- coding: utf-8 -*-
#
import psycopg2 
import psycopg2.extras
import getopt
import sys

try:
    opt,args=getopt.getopt(sys.argv[1:],'s:t:c:')
    if len (opt) == 0 :
        raise NameError ('option')
    schema='public'
    table=''
    connexion_string=''
    class_name="None"
    for o,a in opt:
        if o == '-s':
            schema=a
        elif o == '-t':
            table=a
        elif o == '-c':
            connexion_string=a

    if table == '':
        raise NameError('table')
except:
    print "Utilisation "+sys.argv[0]+" -s nom_du_schema -t nom de la table + c connexion string"
    print 'example -t jrnxc -c "dbname=xxx user=xx port=xx  password=xxx"'
    print """
    This utility create a file, this file can be given as input to the script
    create_phpclass.py with the option -f
    This will create the corresponding PHP File that you need to put in the include folder
    """
    sys.exit(-1)

cnx=psycopg2.connect (connexion_string)
curs=cnx.cursor(cursor_factory=psycopg2.extras.DictCursor)

curs.execute('''
 SELECT
  columns.column_name,columns.data_type,columns.column_default
FROM
  information_schema.columns
WHERE
 columns.table_schema=%s
 and columns.table_name=%s
''',(schema,table))
record=curs.fetchall()
file_name=table+"_struct.txt"
file=open(file_name,'w+')

# class_name
world=table.split('_')
worlds=[]
for i in world:
    worlds.append(i.capitalize())

class_name='_'.join(worlds)
class_name=class_name+"_SQL"

file.write(class_name+"\n")
file.write(schema+"."+table+"\n")
record.reverse()
for l in record:
    col_name,col_type,col_default=l
    file.write ("%s\t|%s\t|%s\n"%(col_name,col_type,col_default))

file.close()
print "file %s has been created "%(file_name)
print "check that the first column is the primary key"
