<?php

namespace Minicup\Misc;

use Kdyby\Replicator\Container as RContainer;
use LeanMapper\Entity;
use Nette\Forms\Container as FContainer;
use Nette\Forms\Controls\SubmitButton;
use Nette\MemberAccessException;
use Nette\Utils\Callback;

class EntitiesReplicatorContainer extends RContainer
{
    /** @var  Entity[] */
    protected $entities;

    /** @var array */
    protected $created = array();

    /**
     * @param callable $factory
     * @param int      $createDefault
     * @param array    $entities
     */
    public function __construct($factory, $createDefault, array $entities)
    {
        parent::__construct($factory, $createDefault);
        $this->entities = $entities;
    }

    /**
     * create default
     */
    protected function createDefault()
    {
        if (!$this->createDefault) {
            return;
        }

        if (!$this->getForm()->isSubmitted()) {
            /** @var Entity[] $entity */
            foreach (array_slice($this->entities, 0, $this->createDefault) as $entity) {
                $this->createOne($entity->id);
            }

        }
    }

    /**
     * create component by name
     *
     * @param string $name
     * @return FContainer
     */
    protected function createComponent($name)
    {
        $container = $this->createContainer($name);
        $container->currentGroup = $this->currentGroup;
        $this->addComponent($container, $name);
        $_entity = NULL;
        /** @var Entity $entity */
        foreach ($this->entities as $entity) {
            if ((int)$name === $entity->id) {
                $_entity = $entity;
            }
        }
        Callback::invoke($this->factoryCallback, $container, $_entity);

        return $this->created[$container->name] = $container;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->createDefault || count($this->entities) === 0;
    }


    /** @var bool */
    private static $registered = FALSE;

    /**
     * @param string $methodName
     * @return void
     */
    public static function register($methodName = 'addDynamic')
    {
        if (self::$registered) {
            FContainer::extensionMethod(self::$registered, function () {
                throw new MemberAccessException;
            });
        }

        FContainer::extensionMethod($methodName, function (FContainer $_this, $name, $factory, array $entities, $createDefault = 0) {
            $control = new EntitiesReplicatorContainer($factory, $createDefault, $entities);
            $control->currentGroup = $_this->currentGroup;
            return $_this[$name] = $control;
        });

        if (self::$registered) {
            return;
        }

        SubmitButton::extensionMethod('addRemoveOnClick', function (SubmitButton $_this, $callback = NULL) {
            $_this->setValidationScope(FALSE);
            $_this->onClick[] = function (SubmitButton $button) use ($callback) {
                $replicator = $button->lookup(__NAMESPACE__ . '\Container');
                /** @var EntitiesReplicatorContainer $replicator */
                if (is_callable($callback)) {
                    Callback::invoke($callback, $replicator, $button->parent);
                }
                $form = $button->getForm(FALSE);
                if ($form) {
                    $form->onSuccess = array();
                }
                $replicator->remove($button->parent);
            };
            return $_this;
        });

        SubmitButton::extensionMethod('addCreateOnClick', function (SubmitButton $_this, $allowEmpty = FALSE, $callback = NULL) {
            $_this->onClick[] = function (SubmitButton $button) use ($allowEmpty, $callback) {
                $replicator = $button->lookup(__NAMESPACE__ . '\Container');
                /** @var EntitiesReplicatorContainer $replicator */
                if (!is_bool($allowEmpty)) {
                    $callback = Callback::closure($allowEmpty);
                    $allowEmpty = FALSE;
                }
                if ($allowEmpty === TRUE || $replicator->isAllFilled() === TRUE) {
                    $newContainer = $replicator->createOne();
                    if (is_callable($callback)) {
                        Callback::invoke($callback, $replicator, $newContainer);
                    }
                }
                $button->getForm()->onSuccess = array();
            };
            return $_this;
        });

        self::$registered = $methodName;
    }


}