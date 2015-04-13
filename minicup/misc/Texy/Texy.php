<?php

namespace Minicup\Misc;

use Nette\Application\LinkGenerator;
use Nette\Utils\Strings;

class Texy extends \Texy
{
    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var  string */
    private $destinationPrefix;

    /**
     * @param $destinationPrefix
     * @param LinkFactory $linkGenerator
     */
    public function __construct($destinationPrefix, LinkGenerator $linkGenerator)
    {
        parent::__construct();
        $this->destinationPrefix = $destinationPrefix;
        $this->linkGenerator = $linkGenerator;
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
     * translate [Presenter:action arg1, arg2] to link with LinkFactory
     * @param string $text
     * @return string
     */
    public function replaceLinks($text)
    {
        $me = $this;
        return Strings::replace($text, '#\[([A-z]*:[A-z]*)( [A-z-, ]*)?\]#', function ($matches) use ($me) {
            $destination = $me->destinationPrefix .$matches[1];
            $args = array();
            if (count($matches) > 2) {
                $args = Strings::trim($matches[2]);
                $args = Strings::replace($args, '# *#');
                $args = Strings::split($args, '#,#');
            }
            $link = $me->linkGenerator->link($destination, $args);
            return "[{$link}]";
        });
    }

}