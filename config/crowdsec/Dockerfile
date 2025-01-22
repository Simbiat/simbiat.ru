FROM crowdsecurity/crowdsec:v1.6.3-debian

# Need git to clone latest version of CoreRuleSet since at the time of writing CrowdSec is using very old version (4.0.0-RC1 or something like that), which is more than a year old.
RUN apt-get -y update&&apt-get -y upgrade&&apt-get -y install git&&apt-get -y autoremove
