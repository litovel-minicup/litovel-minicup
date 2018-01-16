<?php

namespace Minicup\Misc;

use Minicup\AdminModule\Presenters\BaseAdminPresenter;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Repository\TagRepository;
use Minicup\Presenters\BasePresenter;
use Nette\Application\AbortException;


/**
 * @method BasePresenter getPresenter($need = TRUE)
 */
trait HandleTagsTrait
{
    /** @var TagRepository @inject */
    public $tagRepository;

    /**
     * Provide data about tags for select2 by optional term in post parameters
     *
     * @throws AbortException
     */
    public function handleTags()
    {
        $presenter = $this->getPresenter();
        $tags = $this->tagRepository->findLikeTerm(
            $presenter->getRequest()->getPost('term'),
            $presenter->category->year,
            $presenter instanceof BaseAdminPresenter
        );
        $results = [];
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $results[] = ['id' => $tag->id, 'text' => $tag->name ?: $tag->slug];
        }
        $presenter->sendJson(['results' => $results]);
    }
}