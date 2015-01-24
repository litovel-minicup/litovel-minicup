<?php

namespace Minicup\Misc;


use Nette\Utils\Strings;
use Nextras\Application\LinkFactory;

class Texy extends \Texy
{
    /** @var  LinkFactory */
    private $linkFactory;

    /** @var  string */
    private $destinationPrefix;

    /**
     * @param $destinationPrefix
     * @param LinkFactory $linkFactory
     */
    public function __construct($destinationPrefix, LinkFactory $linkFactory)
    {
        parent::__construct();
        $this->destinationPrefix = $destinationPrefix;
        $this->linkFactory = $linkFactory;
    }

    /**
     * @param string $text
     * @param bool $singleLine
     * @return string
     */
    public function process($text, $singleLine = FALSE)
    {
        $text = $this->replaceLinks($text);
        return parent::process($text, $singleLine);
    }

    /**
     * process [Presenter:action arg1, arg2] to link
     * @param $text
     * @return string
     */
    public function replaceLinks($text)
    {
        $me = $this;
        return Strings::replace($text, '#\[([A-z]*:[A-z]*)( [A-z, ]*)?\]#', function ($match) use ($me) {
            $destination = $me->destinationPrefix . $match[1];
            $args = array();
            if (count($match) > 2) {
                $args = Strings::trim($match[2]);
                $args = Strings::replace($args, '# *#');
                $args = Strings::split($args, '#,#');
            }
            $link = $me->linkFactory->link($destination, $args);
            return '[' . $link . ']';
        });
    }

}