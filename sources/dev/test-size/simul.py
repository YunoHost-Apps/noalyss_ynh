#!/usr/bin/python
#
#
#   This file is part of NOALYSS.
#
#   NOALYSS is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   NOALYSS is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with NOALYSS; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#/
# $Revision$
# Copyright Author Dany De Bontridder danydb@aevalys.eu

import random
import getopt 
import sys


	 
			 
def usage():
	print """
	For use with the demo database, this utility helps
	you to create differente kind of databases for tuning
	and improve the performance
	parameters are :
		-h help
		-s generate a sql file for a small database test
		-l generate a sql file for a large database test
		-x generate a extra large sql for a huge database test
		-y year 
		-p first periode  of the year (parm_periode.p_id)
		-n number of year
	"""
	sys.exit(-1)

def Add_Attribut_Fiche(p_jft,p_f,p_ad_id,p_value):
	# Ajout du nom
	#insert into fiche_detail(jtf_id,f_id,ad_id,ad_value)
	att="%d\t%d\t%d\t%s" % (p_jft,p_f,p_ad_id,p_value)
	return att

def Creation_fiche (p_seq_f_id,p_seq_jft_id,p_fd_id,p_type,p_base_poste,p_nbfiche):
	fiche=[]
	poste_comptable=[]
	Attribut=[]
	for i in range (0,p_nbfiche):
		#def Creation fiche :
		#print "insert into fiche(f_id,fd_id)values (%d,%d);" % (p_seq_f_id,p_fd_id)
		fiche.append("%d\t%d" % (p_seq_f_id,p_fd_id))
		# ajout nom
		nom="%s numero %08d" % (p_type,i+100)
		t2=Add_Attribut_Fiche(p_seq_jft_id,p_seq_f_id,1,nom)
		Attribut.append(t2)
		#poste comptable
		str_poste_comptable='%s%04d'% (p_base_poste,i+100)
		# print "insert into tmp_pcmn (pcm_val,pcm_lib,pcm_val_parent) values (%s,'%s',%s); " % (poste_comptable,nom,p_base_poste)
		poste_comptable.append("%s\t%s\t%s" %  (str_poste_comptable,nom,p_base_poste))
		p_seq_jft_id+=1
		t2=Add_Attribut_Fiche(p_seq_jft_id,p_seq_f_id,5,str_poste_comptable)
		Attribut.append(t2)
		p_seq_jft_id+=1
		str_quick_code="FID%06d" % (p_seq_f_id)
		t2=Add_Attribut_Fiche(p_seq_jft_id,p_seq_f_id,23,str_quick_code)
		Attribut.append(t2)

		p_seq_f_id+=1
		p_seq_jft_id+=1
	print "\\echo insert into fiche"
	print "copy fiche(f_id,fd_id) from stdin;"
	for e in fiche: print e
	print "\."
	print "\\echo insert into tmp_pcmn"
	print "copy tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) from stdin;"
	for e in poste_comptable: print e
	print "\."
	print "\\echo insert into fiche_detail"
	print "copy fiche_detail(jft_id,f_id,ad_id,ad_value) from stdin;"
	for e in Attribut: print e
	print "\."
	


def Create_year(pn_year,pn_nb_year):
	print "delete from parm_periode;"
	nb_id=1
	print "\\echo insert into tmp_pcmn"
	print "copy parm_periode (p_id,p_start,p_end,p_exercice,p_closed,p_central) from stdin;"
	for i in range (0,pn_nb_year):
	  for m in range (1,13):
	       nb_day=30
	       if m in [1,3,5,7,8,10,12]: nb_day=31
	       if m == 2 and (i+pn_year)%4 == 0 : nb_day=29
	       if m == 2 and (i+pn_year)%4 != 0 : nb_day=28
	       print ("%d,'%s-%02d-%02d','%s-%02d-%02d','%s'")%(nb_id,i+pn_year,m,1,i+pn_year,m,nb_day,i+pn_year)
	       nb_id=nb_id+1
	print "\."

	

jrn_id=1
jrnx_id=1

def Creation_operation(p_base,p_type,p_year):
	global jrnx_id
	global jrn_id
	#jrn="insert into jrn (jr_def_id,jr_montant,jr_comment,jr_date,jr_grpt_id,jr_internal,jr_tech_per)"
	jrn="%d\t%d\t%.2f\t%s\t%02d.%02d.%d\t%d\t%s\t%d"
	#jrnx="insert into jrnx (j_date,j_montant,j_poste,j_grpt,j_jrn_def,j_debit,j_tech_user,j_tech_per)"
	jrnx="%d\t%02d.%02d.%d\t%.2f\t%s\t%d\t%d\t%s\tSIMULATION\t%d"
	array_jrnx=[]
	array_jrn=[]
	start_periode=periode
	end_periode=periode+11
	for loop_periode in range (start_periode,end_periode):
		for loop_day in range (1,28):
			for loop_op in range (0,nb_per_day):
				j_montant=round(random.randrange(100,5000)/100.0,2)
				j_tva=round(j_montant*0.21,2)
				month=loop_periode-start_periode+1
				if p_type == 'V':
					j_internal='1VEN-01-%d' % (p_base)
					j_client='400%04d' % (random.randrange(1,nb_fiche)+100)
					#jrnx1=jrnx % (loop_day,loop_periode-39,j_montant,j_client,p_base,2,'true',loop_periode)
					array_jrnx.append(jrnx % (jrnx_id,loop_day,month,p_year,j_montant,j_client,p_base,2,'true',loop_periode))
					jrnx_id=jrnx_id+1
					#print jrnx1
					array_jrnx.append(jrnx % (jrnx_id,loop_day,month,p_year,j_tva,'4511',p_base,2,'true',loop_periode))
					jrnx_id=jrnx_id+1
					#print jrnx1
					total=j_montant+j_tva
					array_jrnx.append( jrnx % (jrnx_id,loop_day,month,p_year,total,'700',p_base,2,'false',loop_periode))
					jrnx_id=jrnx_id+1
					#print jrnx1
					array_jrn.append(jrn%(jrn_id,2,total,j_internal,loop_day,month,p_year,p_base,j_internal,loop_periode))
					jrn_id=jrn_id+1
					#print jrn1
					p_base+=1
				if p_type== 'A':
					j_internal='1ACH-01-%d' % (p_base)
					j_fournisseur='440%04d' % (random.randrange(0,nb_fiche)+100)
					j_charge='61%04d' % (random.randrange(0,nb_charge)+100)
					array_jrnx.append(jrnx%(jrnx_id,loop_day,month,p_year,j_montant,j_fournisseur,p_base,3,'false',loop_periode))
					jrnx_id=jrnx_id+1
					#print jrnx1
					array_jrnx.append(jrnx % (jrnx_id,loop_day,month,p_year,j_tva,'4111',p_base,3,'false',loop_periode))
					jrnx_id=jrnx_id+1
					#print jrnx1
					total=j_montant+j_tva
					array_jrnx.append(jrnx % (jrnx_id,loop_day,month,p_year,total,j_charge,p_base,3,'true',loop_periode))
					jrnx_id=jrnx_id+1
					#print jrnx1
					array_jrn.append(jrn%(jrn_id,3,total,j_internal,loop_day,month,p_year,p_base,j_internal,loop_periode))
					jrn_id=jrn_id
					##print jrn1
					p_base+=1
				if p_type== 'O':
					j_internal='4ODS-01-%d' % (p_base)
					j_banque='400'
					j_charge='440'
					array_jrnx.append(jrnx%(jrnx_id,loop_day,month,p_year,j_montant,j_banque,p_base,4,'false',loop_periode))
					jrnx_id=jrnx_id+1
					array_jrnx.append(jrnx % (jrnx_id,loop_day,month,p_year,j_montant,j_charge,p_base,4,'true',loop_periode))
					jrnx_id=jrnx_id+1
					#print jrnx1
					array_jrn.append(jrn%(jrn_id,4,j_montant,j_internal,loop_day,month,p_year,p_base,j_internal,loop_periode))
					jrn_id=jrn_id+1
					##print jrn1
					p_base+=1
				if p_type== 'F':
					j_internal='1FIN-01-%d' % (p_base)
					j_banque='550'
					j_charge='400'
					array_jrnx.append(jrnx%(jrnx_id,loop_day,month,p_year,j_montant,j_banque,p_base,1,'false',loop_periode))
					jrnx_id=jrnx_id+1
					array_jrnx.append(jrnx % (jrnx_id,loop_day,month,p_year,j_montant,j_charge,p_base,1,'true',loop_periode))
					jrnx_id=jrnx_id+1
					#print jrnx1
					array_jrn.append(jrn%(jrn_id,1,j_montant,j_internal,loop_day,month,p_year,p_base,j_internal,loop_periode))
					jrn_id=jrn_id+1
					##print jrn1
					p_base+=1
	
	print "\\echo insert into jrnx"
	print "copy  jrnx (j_id,j_date,j_montant,j_poste,j_grpt,j_jrn_def,j_debit,j_tech_user,j_tech_per) from stdin;"
	for e in array_jrnx: print e
	print "\."
	print "\\echo insert into jrn"
	print """copy jrn (jr_id,jr_def_id,jr_montant,jr_comment,jr_date,jr_grpt_id,jr_internal,jr_tech_per) from stdin;"""
	for e in array_jrn: print e
	print "\."

################################################################################
#  MAIN
################################################################################
if  len(sys.argv) == 1  :
	usage()

is_year=0
is_periode=0
cmd_line=sys.argv[1:]
try :
	a1,a2=getopt.getopt(cmd_line,"slxhy:p:n:",['small','large','extra-large','help','year','periode','nbexercice'])
except getopt.GetoptError,msg:
	 print "ERROR "
	 print msg.msg
	 usage()
	 
nb_year=1	 
for option,value in a1:
	if option in ('-h','--help'):
		usage()
	if option in ('-s','--small'):
		nb_fiche=120
		nb_charge=18
		nb_per_day=5
	if option in ('-l','--large'):
		nb_fiche=5000
		nb_charge=350
		nb_per_day=50
	if option in ('-x','--extra-large'):
		nb_fiche=10000
		nb_charge=1500
		nb_per_day=500
        if option in ('-y','--year'):
		year=int(value)
		is_year=1
	if option in ('-p','--periode'):
		periode=int(value)
		is_periode=1
	if option in ('-n','--nbexercice'):
		nb_year=int(value)
	


if is_year == 0 :
	print "Erreur l'annee est obligatoire"
	sys.exit(-1)
if is_periode == 0 :
	print "Erreur periode (p_id.parm_periode) obligatoire"
	sys.exit(-1)
print '\\timing'
print 'set search_path to public,comptaproc;'
# print "\\set ON_ERROR_STOP on"
# print "begin;"
print "set DateStyle=European;"
# fd_id => client
fd_id=2
# type fiche
type='Client'

# numero de sequence fiche
f_id=1000
# numero de sequence jnt_fic_att_value
jft_id=1000
# poste comptable
base_poste='400'

Creation_fiche(f_id,jft_id,fd_id,type,'400',nb_fiche)

# fournisseur
fd_id=4
type='Fournisseur'
f_id+=nb_fiche+100
jft_id+=20*nb_fiche
base_poste='440'

Creation_fiche(f_id,jft_id,fd_id,type,base_poste,nb_fiche)

# Creation Service et bien divers
fd_id=5
type='Charge '
f_id+=nb_fiche+100
jft_id+=20*nb_fiche
base_poste='61'
tot_op=nb_per_day*365

Creation_fiche(f_id,jft_id,fd_id,type,base_poste,nb_charge)

for i in range(0,nb_year):
  #Creation_operation Vente
  Creation_operation(1000,'V',year+i)

  #Creation_operation Achat
  Creation_operation((1+i)*2*tot_op+10000,'A',year+1)

  #Creation_operation FIN
  Creation_operation((1+i)*3*tot_op+10000,'F',year+1)

  #Creation_operation ODS
  Creation_operation((1+i)*4*tot_op+10000,'O',year+1)

print "commit;"
