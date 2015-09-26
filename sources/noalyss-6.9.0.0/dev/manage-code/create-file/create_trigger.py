#!/usr/bin/python
#
# Give the code for a trigger
import sys, getopt
def help():
    print """
    option are -h for help
               -d default trigger for tech_date
               -a action : insert update or delete or a combination separated by comma
	       -t table name
               -s schema name
    """
def main():
    try:
        opts,args=getopt.getopt(sys.argv[1:],'hda:t:s:',['help','tech_date','action','table','schema'])
    except getopt.GetOptError, err:
        print str(err)
        help()
        sys.exit(-1)
    table_name=''
    action=''
    schema=''
    tech_date=False
    for option,value in opts:
        if option in ('-a','--action'):
            action=value
        elif option in ('-h','--help'):
            help()
            sys.exit(-1)
        elif option in ('-t','--table'):
            table_name=value
        elif option in ('-s','--schema'):
            schema=value
        elif option in ('-d','--tech_date'):
            tech_date=True
    if table_name=='':
        help()
        print "The table name is missing"
        sys.exit(-2)

    if schema == '':
	schema='public'

    if not tech_date and action == '' :
        help()
        print "No action specified "
        sys.exit(-3)

    print ('CREATE OR REPLACE FUNCTION '+schema+'.'+table_name+"_trg"+'() ')
    print (' returns trigger ')
    print (' as ')
    print ('$_BODY_$')
    print ('declare ')
    print ('begin')
    if tech_date : 
	print (' NEW.tech_date=now() ;')
    else :
        print (' -- insert your code here ')
    print ('return NEW;')
    print ('end;')
    print ('$_BODY_$ LANGUAGE plpgsql;')


    print ('CREATE TRIGGER '+table_name+"_trg")
    print (" BEFORE / AFTER ")
    if action == '' and tech_date : 
	print (" INSERT OR UPDATE ")
    elif len(action.split(',')) > 0:
	a_action=action.split(',')
	str_or=''
	for e in a_action:
		print str_or+(e.upper())
		str_or=" OR "
    else:
	print (action.upper())

    print (" on "+schema+'.'+table_name)
    print (" FOR EACH ROW EXECUTE PROCEDURE "+schema+'.'+table_name+"_trg();")



main()
