<?php

namespace Minicup\Misc;


use Minicup\Model\Entity\MatchTerm;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Manager\PhotoManager;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplate;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Http\IRequest;
use Nette\InvalidArgumentException;
use Nette\SmartObject;
use Nette\Utils\Html;
use Nette\Utils\Json;
use Nette\Utils\Strings;

class FilterLoader
{
    use SmartObject;

    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var Texy */
    private $texy;
    /**
     * @var string
     */
    private $wwwPath;
    /** @var IRequest */
    private $request;

    /**
     * @param string        $wwwPath
     * @param LinkGenerator $linkGenerator
     * @param IRequest      $request
     * @param Texy          $texy
     */
    public function __construct(string $wwwPath,
                                LinkGenerator $linkGenerator,
                                IRequest $request,
                                Texy $texy)
    {
        $this->linkGenerator = $linkGenerator;
        $this->texy = $texy;
        $this->wwwPath = $wwwPath;
        $this->request = $request;
    }


    /**
     * @param ITemplate|Template $template
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
            return $latte->invokeFilter('date', [$matchTerm->day->day, 'j. n.']);
        });

        $template->addFilter('matchStart', function (MatchTerm $matchTerm) use ($latte) {
            return $latte->invokeFilter('date', [$matchTerm->start, 'G:i']);
        });

        $template->addFilter('matchEnd', function (MatchTerm $matchTerm) use ($latte) {
            return $latte->invokeFilter('date', [$matchTerm->end, 'G:i']);
        });

        $template->addFilter('termDiff', function (MatchTerm $first, MatchTerm $second) use ($latte) {
            $diff = date_diff($first->start, $second->start);
            return $diff->h + $diff->i / 60.;
        });

        $template->addFilter('toJson', function (array $array) {
            return Json::encode($array);
        });

        $template->addFilter('relDate', function ($time) {
            $seconds = time() - strtotime((string)$time);
            $minutes = floor($seconds / 60);
            $hours = floor($minutes / 60);
            $days = floor($hours / 24);
            $months = floor($days / 30);
            $years = floor($days / 365);
            if ($years >= 2) {
                return "před $years lety";
            } elseif ($years === 1) {
                return 'před rokem';
            } elseif ($months >= 2) {
                return "před $months měsíci";
            } elseif ($months === 1) {
                return 'před měsícem';
            } elseif ($days >= 2) {
                return "před $days dny";
            } elseif ($hours >= 2) {
                return "před $hours hodinami";
            } elseif ($hours === 1) {
                return 'před hodinou';
            } elseif ($minutes >= 2) {
                return "před $minutes minutami";
            } elseif ($minutes === 1) {
                return 'před minutou';
            } elseif ($seconds >= 0) {
                return 'před chvílí';
            }
            return 'v budoucnu';
        });

        $template->addFilter('dayName', function ($time, $len = 2) use ($latte) {
            $name = $latte->invokeFilter('date', [$time, 'w']);
            $names = ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'];
            return Strings::substring($names[$name], 0, $len);
        });

        $template->addFilter('photo', function (Photo $photo, $type = PhotoManager::PHOTO_THUMB) use ($generator) {
            return $generator->link(":Media:$type", [$photo->filename]);
        });

        $template->addFilter('texy', function ($string) use ($texy) {
            return $texy->process($string);
        });

        $template->addFilter('ogImage', function (Photo $photo, $size = PhotoManager::PHOTO_MEDIUM) use ($generator) {
            if (!isset(PhotoManager::$resolutions[$size])) {
                throw new InvalidArgumentException('Unknown photo size.');
            }
            $el = Html::el();
            $el->addHtml(
                Html::el('meta', ['property' => 'og:image', 'content' => $generator->link("Media:$size", [$photo->filename])])
            );
            $el->addHtml(
                Html::el('meta', ['property' => 'og:image:width', 'content' => PhotoManager::$resolutions[$size][0]])
            );
            $el->addHtml(
                Html::el('meta', ['property' => 'og:image:height', 'content' => PhotoManager::$resolutions[$size][1]])
            );
            return $el;
        });

        $template->addFilter('clubLogo', function (TeamInfo $teamInfo, string $cssClass = '') {
            $el = Html::el('img');
            $assetsPath = "assets/img/logos/{$teamInfo->category->year->slug}/{$teamInfo->slug}.png";
            if (!file_exists("{$this->wwwPath}/$assetsPath"))
                return '';

            $el->addAttributes([
                'src' => $this->request->getUrl()->getBaseUrl() . $assetsPath,
                'class' => $cssClass
            ]);
            return $el;
        });

        return $template;
    }
}