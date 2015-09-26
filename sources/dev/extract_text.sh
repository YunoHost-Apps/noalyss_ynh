#!/bin/bash
# Brief : extract strings from the file, in order to update a
# po file. It is used for the translation
#
#
# This file is a part of NOALYSS under GPL
# Author D. DE BONTRIDDER danydb@aevalys.eu
echo "Extract"
cd ..
# CATALOG
xgettext -L PHP -j --from-code=UTF-8 -p html/lang/ html/*.php include/*.php include/template/*.php include/ext/*/*.php include/ext/*/include/*.php  include/ext/*/include/template/*.php 

# For dutch
echo "Dutch"
msgmerge -U -s html/lang/nl_NL/LC_MESSAGES/messages.po html/lang/messages.po

#For english
echo "English"
msgmerge -U -s html/lang/en_US/LC_MESSAGES/messages.po html/lang/messages.po

#For new language
# export LOCAL=nl_NL
# msginit --locale=$LOCAL -i html/lang/messages.po -o html/lang/$LOCAL/LC_MESSAGES/messages.po 
