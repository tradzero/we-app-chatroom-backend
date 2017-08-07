<?php

namespace Ws;

use Adarts\Dictionary;

class Filter
{
    protected $dict;
    protected $bannedWords;

    public function __construct()
    {
        $this->initDict();
    }

    /**
     * 判断文字是否包含敏感词
     *
     * @return [boolean] $result 是否包含敏感词
     */
    public function check($text)
    {
        $result = $this->dict->seek($text)->current();

        return is_null($result);
    }

    /**
     * 过略文本
     *
     * @return void
     */
    public function filterText($text)
    {
        $seeker = $this->dict->seek($text);

        if (is_null($seeker->current())) {
            return $text;
        } else {
            $words = $this->getWords($seeker);

            $result = str_replace($words, '*', $text);
            
            return $result;
        }
    }

    protected function initDict()
    {
        $filePath = __DIR__ . '/../cache/dict_cache';

        if (file_exists($filePath)) {
            $packed = file_get_contents($filePath);
            $dict = unserialize($packed);
            $this->dict = $dict;
        } else {
            $this->buildDict();
        }
    }

    protected function buildDict()
    {
        $dict = new Dictionary();

        $this->loadBannedWord();

        foreach ($this->bannedWords as $word) {
            $word = str_replace(PHP_EOL, '', trim($word));
            $dict->add($word);
        }

        $dict->confirm();

        $this->saveDictCache($dict);

        $this->dict = $dict;
    }

    protected function saveDictCache($dict)
    {
        $filePath = __DIR__ . '/../cache/dict_cache';

        $packed = serialize($dict);

        file_put_contents($filePath, $packed);
    }

    protected function loadBannedWord()
    {
        $filePath = __DIR__ . '/../storage/banned_words.txt';

        $lines = file($filePath);

        $bannedWords = [];

        foreach ($lines as $line) {
            array_push($bannedWords, base64_decode(trim($line)));
        }

        $this->bannedWords = $bannedWords;
    }

    protected function getWords($seeker)
    {
        $words = [];
        foreach ($seeker as $word) {
            array_push($words, $this->dict->getWordByState($word));
        }
        return $words;
    }
}