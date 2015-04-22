<?php

namespace Minicup\Misc;


use Minicup\Model\Entity\MatchTerm;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Manager\PhotoManager;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplate;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\InvalidArgumentException;
use Nette\Object;
use Nette\Utils\Json;

class FilterLoader extends Object
{

    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var Texy */
    private $texy;

    /**
     * @param LinkGenerator $linkGenerator
     * @param Texy $texy
     */
    public function __construct(LinkGenerator $linkGenerator, Texy $texy)
    {
        $this->linkGenerator = $linkGenerator;
        $this->texy = $texy;
    }


    /**
     * @param Template $template
     * @return Template
     */
    public function loadFilters(ITemplate $template)
    {
        if (!$template instanceof Template) {
            $type = $template->getReflection()->getName();
            throw new InvalidArgumentException("\$template have to be instance of Nette\\Bridges\\ApplicationLatte\\Template, '$type' given.");
        }

        $latte = $template->getLatte();
        $generator = $this->linkGenerator;
        $texy = $this->texy;

        $template->addFilter('matchDate', function (MatchTerm $matchTerm) use ($latte) {
            return $latte->invokeFilter('date', array($matchTerm->day->day, "j. n."));
        });

        $template->addFilter('matchStart', function (MatchTerm $matchTerm) use ($latte) {
            return $latte->invokeFilter('date', array($matchTerm->start, "G:i"));
        });

        $template->addFilter('matchEnd', function (MatchTerm $matchTerm) use ($latte) {
            return $latte->invokeFilter('date', array($matchTerm->end, "G:i"));
        });

        $template->addFilter('toJson', function (array $array) {
            return Json::encode($array);
        });

        $template->addFilter('relDate', function ($time) {
            $seconds = time() - strtotime($time);
            $minutes = floor($seconds / 60);
            $hours = floor($minutes / 60);
            $days = floor($hours / 24);
            $months = floor($days / 30);
            $years = floor($days / 365);
            if ($years >= 2) {
                return "před $years lety";
            } elseif ($years == 1) {
                return "před rokem";
            } elseif ($months >= 2) {
                return "před $months měsíci";
            } elseif ($months == 1) {
                return "před měsícem";
            } elseif ($days >= 2) {
                return "před $days dny";
            } elseif ($hours >= 2) {
                return "před $hours hodinami";
            } elseif ($hours == 1) {
                return "před hodinou";
            } elseif ($minutes >= 2) {
                return "před $minutes minutami";
            } elseif ($minutes == 1) {
                return "před minutou";
            } elseif ($seconds >= 0) {
                return "před chvílí";
            }
            return "v budoucnu";
        });
        $template->addFilter('photo', function (Photo $photo, $type = PhotoManager::PHOTO_THUMB) use ($generator) {
            // TODO
            return $generator->link(':Media:photo');
        });

        $template->addFilter('texy', function ($string) use ($texy) {
            // TODO: cache processed content
            return $texy->process($string);
        });

        return $template;
    }
}