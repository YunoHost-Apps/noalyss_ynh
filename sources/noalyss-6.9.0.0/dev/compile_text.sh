#!/bin/bash
# Brief : compite  the file .mo, 
# It is used for the translation
#
#
# This file is a part of NOALYSS under GPL
# Author D. DE BONTRIDDER danydb@aevalys.eu
cd ../html/lang
echo "English"
cd en_US/LC_MESSAGES
msgfmt -c -v messages.po
echo "Dutch"
cd ../..
cd nl_NL/LC_MESSAGES
msgfmt -c -v messages.po

