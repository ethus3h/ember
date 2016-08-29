PATH="$PATH:$HOME/Library/Python/3.4/bin:/opt/local/Library/Frameworks/Python.framework/Versions/2.7/bin/:/opt/local/bin/"
g() { cd /Volumes/disk2s1/Web/ && grab-site "$@"; }
warp() {
    cd /Volumes/disk2s1/
    NOWTIME=$(date +%Y.%m.%d.%H.%M.%S.%N)
    mkdir warcprox-"$NOWTIME"
    cd warcprox-"$NOWTIME"
    warcprox &
    sleep 5
    /Applications/Vivaldi.app/Contents/MacOS/Vivaldi --ignore-certificate-errors
}
export -f warp
export EDITOR=/usr/bin/nano

export PATH="$PATH:$HOME/.rvm/bin" # Add RVM to PATH for scripting

iu() { IUIDENTIFIER=$(python -c 'import uuid; print str(uuid.uuid4())')-$(date +%Y.%m.%d.%H.%M.%S.%N)-$(xxd -pu <<< "$(date +%z)"); ia upload  $IUIDENTIFIER --metadata="subject:Uploaded using iu v3; D0FC61AE-A860-11E5-AA02-6FF54124FF49" "$@"; echo 'https://archive.org/download/'$IUIDENTIFIER; }
iud() { IUIDENTIFIER=$(python -c 'import uuid; print str(uuid.uuid4())')-$(date +%Y.%m.%d.%H.%M.%S.%N)-$(xxd -pu <<< "$(date +%z)"); ia upload  $IUIDENTIFIER --metadata="subject:Uploaded using iu v3; D0FC61AE-A860-11E5-AA02-6FF54124FF49" --delete "$@"; echo 'https://archive.org/download/'$IUIDENTIFIER; }
export -f iu;
export -f iud;

# MacPorts
export PATH="/opt/local/bin:/opt/local/sbin:$PATH"

#To fix python by making it see pip packages
export PATH=/usr/bin:$PATH

