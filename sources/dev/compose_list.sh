#!/bin/bash
#
# This file is a part of NOALYSS under GPL
# Author D. DE BONTRIDDER danydb@aevalys.eu

cat <<EOF
This script create a list of all the function on only page,
you must first run doxygen and call this file from the phpcompta directory
EOF
DOC=doc/developper/html
cat $DOC/globals_func.html > $DOC/list_function.html
find $DOC/globals_func*.html |
 xargs awk '/<h3>/,/\/ul/ { print $0; }' >> $DOC/list_function.html 
cat $DOC/list_function.html >  $DOC/globals_func.html
[ $? -eq 0 ]&&echo "********************** DONE ***************"
