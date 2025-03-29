FROM crowdsecurity/crowdsec:v1.6.8-debian

# Need git to clone latest version of CoreRuleSet since at the time of writing CrowdSec is using very old version (4.0.0-RC1 or something like that), which is more than a year old.
RUN apt-get -y update&&apt-get -y --no-install-recommends upgrade&&apt-get -y --no-install-recommends install git&&apt-get -y autoremove&&apt-get clean