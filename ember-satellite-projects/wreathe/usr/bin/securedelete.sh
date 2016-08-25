#!/bin/bash

utente=`id -u`
nome=$(basename "$@")
proprietario=$(ls -alodn "$@"| awk '{print $3}')
  if [ "$utente" == "$proprietario" ];then
  echo $@;
  `kdialog --caption "Secure Delete" --menu "Choose how:" "srm -l" "Fast and unsecure" "srm -f" "Slow and more secure" srm "Very slow and very secure"` "$@"
  else
   kdialog --error "Sorry, you don't own "$nome""
fi
