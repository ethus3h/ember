# Ember

Learn about this project at the Ember Web site: http://futuramerlin.com/

To download all of Ember and the projects it maintains:

Unless you are working on Wreathe, you probably don't need the Wreathe resource repositories. If you do want them, run both of the following commands, otherwise just run the second.

```
declare -a emberWreatheResourceProjects=("wreathe-backgrounds" "wreathe-media-resources" "wreathe-office-resources" "wreathe-typeface-family" "wreathe-typefaces-redist");
```

```
declare -a emberProjects=("ember" "ember-client" "ember-library" "ember-web-site"); declare -a emberSatelliteProjects=("crystallise" "futuramerlin-web-toolkit" "rubberfs" "warcdealer" "wreathe" "wreathe-overlay" "${emberWreatheResourceProjects[@]}"); set -e; mkdir ember; cd ember || exit 1; mkdir ember; cd ember || exit 1; git clone "${emberProjects[@]}"; cd ..; mkdir ember-satellite-projects; cd ember-satellite-projects || exit 1; git clone "${emberSatelliteProjects[@]}"; cd ../.. || exit 1; echo "Finished downloading Ember."
```

To save your changes to the resulting development environment:

```
findemberfolders() { find . -maxdepth 1 -type d -exec sh -c 'cd "$1" || exit 1; git add ./* .[^.]*; git commit -a; git pull; git push -u origin master' -- '{}' \; ; }; set -e; cd ember || exit 1; findemberfolders; cd ../ember-satellite-projects || exit 1; findemberfolders; cd .. || exit 1
```
