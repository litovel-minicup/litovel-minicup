<?php

namespace Minicup\ApiModule\Presenters;


use Minicup\Model\Entity\Category;

class CategoryPresenter extends BaseApiPresenter
{

    /**
     * @param Category $category
     * @throws \Nette\Application\AbortException
     */
    public function actionUpcomingMatches(Category $category): void
    {
        $matches = [];
        foreach (\array_slice($category->matches, 0, 4) as $match) {
            $matches[$match->id] = $match->serialize();
        }
        $this->sendJson(['matches' => $matches]);
    }

}