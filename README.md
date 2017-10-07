# Lesti Merge

Lesti Merge is a Magento 1 module originally created by [Gordon Lesti](https://github.com/GordonLesti/Lesti_Merge) to provide intelligent handle-based CSS/JS merging. This fork serves to improve functionality not provided in the original module.

## Installation

The module is extremely simple to install, download an archive and enable a setting in the admin.

~~~~
cd /microcloud/domains/example/domains/example.com
wget --no-check-certificate https://github.com/sonassi/Lesti_Merge/archive/master.zip -O lesti-merge.zip
unzip lesti-merge.zip
rsync -vPa Lesti_Merge/app/ app/
~~~~

Log into your Magento admin `System > Config > Advanced > Developer` then,

 - Under JavaScript Settings; enable "Merge JS By Handle"
 - Under CSS Settings; enable "Merge CSS By Handle"

## Excluding files

Should you find broken CSS, or JS syntax errors; you can exclude specific files (by filename) using the configuration settings in `System > Config > Advanced > Developer`.

Just enter the respective filename to be excluded, comma separated if there are multiple.
