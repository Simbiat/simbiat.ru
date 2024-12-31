FROM crowdsecurity/crowdsec:v1.6.4-debian

# Need git to clone latest version of CoreRuleSet since at the time of writing CrowdSec is using very old version (4.0.0-RC1 or something like that), which is more than a year old. But we do not want to keep git after that. We also want to remove .git files due to https://youtrack.jetbrains.com/issue/WI-80162/Auto-adds-a-git-repo-from-ignored-directory which just annoys me
RUN apt-get -y update&&apt-get -y upgrade&&apt-get -y install git&&apt-get -y autoremove&&mkdir -p /var/lib/crowdsec/data/coreruleset&&rm -r -f /var/lib/crowdsec/data/coreruleset&&git clone https://github.com/coreruleset/coreruleset /var/lib/crowdsec/data/coreruleset --branch v4.10.0&&apt-get -y remove git&&rm -r -f /var/lib/crowdsec/data/coreruleset/.git*
