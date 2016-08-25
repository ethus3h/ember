#!/bin/bash

# utente=`id -u`
# nome=$(basename "$@")
# proprietario=$(ls -alodn "$@"| awk '{print $3}')
#   if [ "$utente" == "$proprietario" ];then
#   echo $i;
#   `kdialog --caption "Secure Delete" --menu "Choose how:" "srm -l -r" "Fast and unsecure" "srm -f -r" "Slow and more secure" "srm -r" "Very slow and very secure"` "$@"
#   else
#   kdialog --error "Sorry, you don't own "$nome""
#   fi
