PATH="$PATH:$HOME/Library/Python/3.4/bin:/opt/local/Library/Frameworks/Python.framework/Versions/2.7/bin/:/opt/local/bin/"
g() { cd /Volumes/disk2s1/Web/ && grab-site "$@"; }
export EDITOR=/usr/bin/nano

export PATH="$PATH:$HOME/.rvm/bin" # Add RVM to PATH for scripting
# based on http://unix.stackexchange.com/questions/25639/how-to-automatically-record-all-your-terminal-sessions-with-script-utility
#test "$(ps -ocommand= -p $PPID | awk '{print $1}')" == 'script' || (script /Users/elliot/.bash_logs/$(date +"%d-%b-%y_%H-%M-%S")_shell.log)
# rsync -av --progress --exclude=.pch/.pshadow --no-i-r /Volumes/disk2s1/Archive/ 'PermissionGiver@76.179.191.27:/Volumes/Archives/FuturamerlinMultimediaArchive'
#sleep 3; while true; do sleep 0.2; ~/fametrain-autotyper-2015sept24-n3-v2b-yimi-2015sept27.app/Contents/MacOS/Application\ Stub; done

##
# Your previous /Users/elliot/.bash_profile file was backed up as /Users/elliot/.bash_profile.macports-saved_2015-11-18_at_13:41:07
##
iu() { IUIDENTIFIER=$(python -c 'import uuid; print str(uuid.uuid4())')-$(date +%Y.%m.%d.%H.%M.%S.%N)-$(xxd -pu <<< "$(date +%z)"); ia upload  $IUIDENTIFIER --metadata="subject:Uploaded using iu v3; D0FC61AE-A860-11E5-AA02-6FF54124FF49" "$@"; echo 'https://archive.org/download/'$IUIDENTIFIER; }
iud() { IUIDENTIFIER=$(python -c 'import uuid; print str(uuid.uuid4())')-$(date +%Y.%m.%d.%H.%M.%S.%N)-$(xxd -pu <<< "$(date +%z)"); ia upload  $IUIDENTIFIER --metadata="subject:Uploaded using iu v3; D0FC61AE-A860-11E5-AA02-6FF54124FF49" --delete "$@"; echo 'https://archive.org/download/'$IUIDENTIFIER; }
export -f iu;
export -f iud;

# MacPorts Installer addition on 2015-11-18_at_13:41:07: adding an appropriate PATH variable for use with MacPorts.
export PATH="/opt/local/bin:/opt/local/sbin:$PATH"
# Finished adapting your PATH environment variable for use with MacPorts.

#To fix python by making it see pip packages
export PATH=/usr/bin:$PATH

