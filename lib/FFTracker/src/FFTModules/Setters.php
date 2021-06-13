<?php
#Functions used to set settings
declare(strict_types=1);
namespace Simbiat\FFTModules;

trait Setters
{
    #Settings required for Lodestone library
    protected string $useragent = '';
    protected string $language = 'na';
    protected int $maxage = 90;
    public int $maxlines = 50;
    protected string $crestpath = '';

    #############
    #Setters
    #############
    public function setUseragent(string $useragent = ''): self
    {
        $this->useragent = $useragent;
        return $this;
    }

    public function setMaxage(int $maxage = 90): self
    {
        $this->maxage = $maxage;
        return $this;
    }

    public function setMaxlines(int $maxlines = 50): self
    {
        $this->maxlines = $maxlines;
        return $this;
    }

    public function setLanguage(string $language = 'en'): self
    {
        #En is used only for user convinience, in reality it uses NA (North America)
        if ($language === 'en') {
            $language = 'na';
        }
        if (!in_array($language, self::langAllowed)) {
            $language = 'na';
        }
        if (in_array($language, ['jp', 'ja'])) {$language = 'jp';}
        $this->language = $language;
        return $this;
    }

    #############
    #Getters
    #############
    public function getUseragent(): string
    {
        return $this->useragent;
    }

    public function getMaxlines(): int
    {
        if (empty($this->maxlines)) {
            $this->maxlines = 50;
        }
        return $this->maxlines;
    }

    public function getMaxage(): int
    {
        if (empty($this->maxage)) {
            $this->maxage = 90;
        }
        return $this->maxage;
    }

    public function getLanguage(): string
    {
        $language = $this->language;
        if (!in_array($language, self::langAllowed)) {
            $language = 'na';
        }
        if (in_array($language, ['jp', 'ja'])) {$language = 'jp';}
        $this->language = $language;
        return $this->language;
    }

    public function getCrestPath(): string
    {
        $crestpath = $this->crestpath;
        if ($crestpath === '') {
            $crestpath = dirname(__DIR__) . '/Images/merged-crests';
        }
        $this->crestpath = preg_replace('/(.*[^\\\\\/]+)([\\\\\/]+$)/m', '$1', $crestpath).'/';
        return $this->crestpath;
    }
}
